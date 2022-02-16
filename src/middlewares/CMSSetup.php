<?php

namespace hcolab\cms\middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class CMSSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (!Schema::hasTable('entity_versions')) {
            Schema::create('entity_versions', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('entity_versions', function (Blueprint $table){
        if (!Schema::hasColumn("entity_versions", "created_at")) {
                $table->dateTime('created_at', 0)->useCurrent();
        }
        if (!Schema::hasColumn("entity_versions", "updated_at")) {
                $table->dateTime('updated_at', 0)->useCurrent();
        }
        if (!Schema::hasColumn("entity_versions", "deleted_at")) {
                $table->softDeletes('deleted_at', 0)->nullable();
        }
        if (!Schema::hasColumn("entity_versions", "deleted")) {
                $table->tinyInteger('deleted', 0)->default(0);
        }
            
        if (!Schema::hasColumn("entity_versions", "version")) {
                $table->double('version')->default(0);
        }

        if (!Schema::hasColumn("entity_versions", "page")) {
                $table->string('page', 255)->nullable();
        }
        });
        
        return $next($request);
    }
}