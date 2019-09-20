<?php

use Illuminate\Support\Facades\Schema;
use Core\Foundation\Database\OpxBlueprint;
use Core\Foundation\Database\OpxMigration;

class CreatePermissionGroupsTable extends OpxMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->schema->create('permission_groups', static function (OpxBlueprint $table) {
            $table->id();
            $table->name();
            $table->boolean('default')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('permission_groups');
    }
}
