<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    public function getAppSetting()
    {
        $setting = Setting::first() ?? null;
        return response()->json($setting);
    }

    /**
     * Update the app setting
     *
     * @param SettingRequest $request
     * @param Setting $setting
     * @return \Illuminate\Http\Response $response
     */
    public function editSetting(SettingRequest $request, Setting $setting)
    {
        $setting->update($request->all());

        if ($request->hasFile('app_logo')) {
            if ($media = $setting->getFirstMedia('settings')) {
                $media->delete();
            }

            $setting->addMediaFromRequest('app_logo')->toMediaCollection('settings');
        }

        return response()->json(
            Setting::first() ?? null
        );
    }
}
