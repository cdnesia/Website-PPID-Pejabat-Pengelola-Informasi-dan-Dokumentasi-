<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;

class SettingController extends Controller
{
    public function show(): SettingResource
    {
        return new SettingResource(Setting::current());
    }

    public function update(UpdateSettingRequest $request): SettingResource
    {
        $setting = Setting::current();
        $setting->update($request->safe()->except('logo'));

        if ($request->hasFile('logo')) {
            $setting->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        return new SettingResource($setting);
    }
}
