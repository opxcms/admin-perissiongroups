<?php

use Illuminate\Support\Facades\Schema;
use Core\Foundation\Database\OpxBlueprint;
use Core\Foundation\Database\OpxMigration;

class CreatePermissionGroupHasPermissionTable extends OpxMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->schema->create('permission_group_has_permission', static function (OpxBlueprint $table) {
            $table->integer('group_id');
            $table->string('permission');

            $table->primary(['group_id','permission']);
            $table->index(['group_id','permission']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('permission_group_has_permission');
    }
}
