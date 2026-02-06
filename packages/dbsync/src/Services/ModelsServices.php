<?php

namespace Aldids\FilamentDbSync\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Aldids\FilamentDbSync\Models\DbSync;

class ModelsServices
{
    /**
     * Get the models that want to be synced
     */
    public static function getModelsWantToBeSynced(): array
    {
        $models = [];

        switch (self::modelsConfig('auto_scan')) {
            case 1:
            default:
                $scanned_models = self::turnModelsFilesToClassName(self::scanModelsDirectory());
                $excluded_models_class = (array) self::modelsConfig('excluded');

                $models = array_filter($scanned_models, function ($model) use ($excluded_models_class) {
                    return !in_array(get_class($model), $excluded_models_class);
                });
                break;

            case 0:
                $models = (array) self::modelsConfig('included');
                break;
        }

        return $models;
    }

    public static function modelsConfig($key = null)
    {
        return Config::get('db_sync.models' . ($key ? '.' . $key : ''));
    }

    public static function scanModelsDirectory(): array
    {
        $models = [];
        $modelsDirectory = app_path('Models');

        if (!is_dir($modelsDirectory)) return [];

        $files = scandir($modelsDirectory);
        foreach ($files as $file) {
            if (is_file($modelsDirectory . '/' . $file) && str_ends_with($file, '.php')) {
                $models[] = $modelsDirectory . '/' . $file;
            }
        }

        return $models;
    }

    public static function turnModelsFilesToClassName($files): array
    {
        $models = [];
        $modelsDirectory = app_path('Models');
        foreach ($files as $file) {
            $relative_path = str_replace([$modelsDirectory, '.php', '/'], ['', '', '\\'], $file);
            $class_name = '\\App\\Models\\' . ltrim($relative_path, '\\');
            if (class_exists($class_name)) {
                $models[] = new $class_name();
            }
        }

        return $models;
    }

    public static function modelsTableSchemaDefinition(string $modelsName, string $defaultColumnTypeData = 'string'): array
    {
        $model = new $modelsName;
        $table = $model->getTable();
        $fillable = $model->getFillable();
        $cast = $model->getCasts();

        $schema = array_map(function ($column) use ($cast, $defaultColumnTypeData) {
            return [
                'name' => $column,
                'type' => $cast[$column] ?? $defaultColumnTypeData,
            ];
        }, $fillable);

        return [
            'class' => $modelsName,
            'table_name' => $table,
            'schema' => $schema,
        ];
    }

    public static function getDatas(string $model): array
    {
        return (new $model)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public static function getTableDatas(string $table_name): array
    {
        return DB::table($table_name)->get()->toArray();
    }

    public static function getTablePrimaryKeyFromConfig(string $table_name): string
    {
        $primary_key = 'id';
        $tables_keys = [];

        foreach ((array)self::modelsConfig('column_as_key') as $key => $value) {
            if (class_exists($key)) {
                $tables_keys[(new $key)->getTable()] = $value;
            } else {
                $tables_keys[$key] = $value;
            }
        }

        return $tables_keys[$table_name] ?? $primary_key;
    }

    public static function createTableSchema(array $model_definition, string $plugin_ids): bool
    {
        if (!Schema::hasTable($model_definition['table_name'])) {
            try {
                Schema::create($model_definition['table_name'], function ($table) use ($model_definition) {
                    $table->id();
                    foreach ($model_definition['schema'] as $column) {
                        $type = ($column['type'] === 'hashed') ? 'string' : $column['type'];

                        // Ensure the blueprint method exists
                        if (method_exists($table, $type)) {
                            $table->{$type}($column['name'])->nullable();
                        } else {
                            $table->string($column['name'])->nullable();
                        }
                    }
                    $table->timestamps();
                    $table->softDeletes();
                });
                Log::info('[' . $plugin_ids . '] Table created: ' . $model_definition['table_name']);
                return true;
            } catch (\Throwable $th) {
                Log::error('[' . $plugin_ids . '] Error creating table: ' . $th->getMessage());
                return false;
            }
        }

        return true;
    }

    public static function storeDataToDatabase(string $model_primary_key, array $model_definition, array $model_datas, string $plugin_ids, array $sync_config): void
    {
        $duplicate_data_action = $sync_config['duplicate_data_action'] ?? 'update';
        $schema = $model_definition['schema'];
        $tableName = $model_definition['table_name'];

        try {
            foreach ($model_datas as $model_data) {
                $data = [];

                // Build data array from schema
                foreach ($schema as $column) {
                    $colName = $column['name'];
                    if (in_array($colName, ['created_at', 'updated_at'])) {
                        $data[$colName] = isset($model_data[$colName])
                            ? Carbon::parse($model_data[$colName])->format('Y-m-d H:i:s')
                            : now()->format('Y-m-d H:i:s');
                    } else {
                        $data[$colName] = $model_data[$colName] ?? null;
                    }
                }

                // Ensure timestamps exist if missing from fillable/schema
                if (empty($data['created_at'])) $data['created_at'] = now()->format('Y-m-d H:i:s');
                if (empty($data['updated_at'])) $data['updated_at'] = now()->format('Y-m-d H:i:s');

                if ($duplicate_data_action === 'update') {
                    $exists = DB::table($tableName)
                        ->where($model_primary_key, $model_data[$model_primary_key])
                        ->exists();

                    if ($exists) {
                        DB::table($tableName)
                            ->where($model_primary_key, $model_data[$model_primary_key])
                            ->update($data);
                        continue;
                    }
                }

                // Insert logic (for 'duplicate' action or 'update' where record doesn't exist)
                // Manual ID increment
                $lastId = DB::table($tableName)->max('id') ?? 0;
                $data['id'] = $lastId + 1;

                DB::table($tableName)->insert($data);
            }
        } catch (\Throwable $th) {
            DbSync::create([
                'model' => $model_definition['class'],
                'action' => 'pull',
                'data' => json_encode($model_datas),
                'status' => 'failed',
                'failed_at' => now(),
                'failed_reason' => $th->getMessage(),
            ]);
            Log::error('[' . $plugin_ids . '] Sync Error: ' . $th->getMessage());
        }
    }
}
