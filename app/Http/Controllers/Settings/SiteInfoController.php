<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SiteInfo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\UpdateSiteInfoRequest;
use App\Traits\HasPermissionChecks;


class SiteInfoController extends Controller
{
    use HasPermissionChecks;
    public function edit()
    {
        $this->authorizeAction('website.edit');
        $siteInfo = SiteInfo::first() ?? new SiteInfo();
        
        return Inertia::render('settings/SiteInfo', [
            'siteInfo' => $siteInfo
        ]);
    }


    public function update(UpdateSiteInfoRequest $request)
    {
        $this->authorizeAction('website.edit');
        $siteInfo = SiteInfo::first();

        if ($siteInfo) {
            $siteInfo->update($request->validated());
        } else {
            SiteInfo::create($request->validated());
        }

        return redirect()->back()->with('success', __('Site information updated successfully.'));
    }

}
