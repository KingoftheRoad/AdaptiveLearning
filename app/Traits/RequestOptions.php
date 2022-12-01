<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\Validator;
use stdClass;

trait RequestOptions
{

    public function getRequestOptions($request)
    {
        $options = new stdClass();
        $options->paginate = null;
        $options->paginateNum = null;
        $options->sortBy = null;
        $options->sortDir = null;
        $options->searchBy = null;
        $options->inUse = null;
        $options->inUseType = null;
        $options->isActive = null;
        $options->elementId = null;
        $options->new_alert = null;
        $options->node_level_type_id = null;
        $options->LPState = null;
        $options->nid = null;

        if ($request->exists('page')) {
            $options->paginate = true;
            $options->paginateNum = (int)$request->input('per_page');
        }
        if ($request->exists('sort_by')) {
            $options->sortBy = $request->input('sort_by');
        }
        if ($request->exists('sort_dir')) {
            $options->sortDir = $request->input('sort_dir');
        }
        if ($request->exists('q')) {
            $options->searchBy = $request->input('q');
        }
        if ($request->exists('isActive')) {
            $options->isActive = $request->input('isActive');
        }
        if ($request->exists('inUse')) {
            $options->inUse = $request->input('inUse');
        }
        if ($request->exists('e')) {
            $options->elementId = $request->input('e');
        }
        if ($request->exists('new_alert')) {
            $options->new_alert = $request->input('new_alert');
        }
        if ($request->exists('node_level_type_id')) {
            $options->node_level_type_id = $request->input('node_level_type_id');
        }
        if ($request->exists('LPState')) {
            $options->LPState = $request->input('LPState');
        }
        if ($request->exists('excludeInventory')) {
            $options->excludeInventory = $request->input('excludeInventory');
        }

        return $options;
    }

}
