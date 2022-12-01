<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Models\Settings;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
class SettingsController extends Controller
{
    // Load Common Traits
    use Common;

    /**
     * USE : Admin can set site settings
     */
    public function settings(Request $request){
        try {
            if ($request->isMethod('post')){
                // check image validations
                $validator = Validator::make($request->all(), [
                    'logo_image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                ]);
                if ($validator->fails()) { // If validation is failed then redirect to back
                    return back()->withErrors($validator)->withInput();
                }

                // If check setting the first record is exist
                $existingSettingsData = Settings::first();

                $destinationPath = public_path('/uploads/settings');

                // If logo image is existing into request params
                $logoImageFullPath = '';
                if($request->file('logo_image')){
                    $logo_image = $request->file('logo_image');
                    $logoImageName = rand(10,100000).time().'.'.$logo_image->extension();
                    $img = Image::make($logo_image->path());
                    $img->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$logoImageName);
                    //$destinationPath = public_path('/images');
                    $logo_image->move($destinationPath, $logoImageName);
                    $logoImageFullPath = 'uploads/settings/'.$logoImageName;
                    
                    // Existing image unlink from folder
                    if(!empty($existingSettingsData)){
                        if($existingSettingsData->{cn::SETTINGS_LOGO_IMAGE_COL} != NULL){
                            if (File::exists(public_path("{$existingSettingsData->logo_image}"))) {
                                unlink(public_path("{$existingSettingsData->logo_image}"));
                            }
                        }
                    }
                    
                }

                // If Fav icon is existing into request params
                $favIconFullPath = '';
                if($request->file('fav_icon')){
                    $fav_icon = $request->file('fav_icon');
                    $favIconImageName = rand(10,100000).time().'.'.$fav_icon->extension();
                    $img = Image::make($fav_icon->path());
                    $img->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$favIconImageName);
                    //$destinationPath = public_path('/images');
                    $fav_icon->move($destinationPath, $favIconImageName);
                    $favIconFullPath = 'uploads/settings/'.$favIconImageName;

                    // Existing fav-icon image unlink from folder
                    if(!empty($existingSettingsData)){
                        if($existingSettingsData->{cn::SETTINGS_FAV_ICON_COL} != NULL){
                            if (File::exists(public_path("{$existingSettingsData->fav_icon}"))) {
                                unlink(public_path("{$existingSettingsData->fav_icon}"));
                            }
                        }
                    }
                }
                
                if(isset($existingSettingsData) && !empty($existingSettingsData)){  // If first record is exists then update settings records
                    // If fav icon not uploaded and existing fav icon is available then store existing value in to variable
                    if(empty($favIconFullPath) && isset($existingSettingsData->{cn::SETTINGS_FAV_ICON_COL})){
                        $favIconFullPath = $existingSettingsData->{cn::SETTINGS_FAV_ICON_COL};
                    }
                    // If fav logo image not uploaded and existing logo_image is available then store existing value in to variable
                    if(empty($logoImageFullPath) && isset($existingSettingsData->{cn::SETTINGS_LOGO_IMAGE_COL})){
                        $logoImageFullPath = $existingSettingsData->{cn::SETTINGS_LOGO_IMAGE_COL};
                    }
                    // Update existing records
                    $this->StoreAuditLogFunction($request->all(),'Settings',cn::SETTINGS_ID_COL,$UpdateSettings = Settings::first()->id,'Update Settings',cn::SETTINGS_TABLE_NAME,'');
                    $UpdateSettings = Settings::first()->update([
                        cn::SETTINGS_SITE_NAME_COL => $request->{cn::SETTINGS_SITE_NAME_COL},
                        cn::SETTINGS_SITE_URL_COL => $request->{cn::SETTINGS_SITE_URL_COL},
                        cn::SETTINGS_EMAIL_COL => $request->{cn::SETTINGS_EMAIL_COL},
                        cn::SETTINGS_CONTACT_NUMBER_COL => $request->{cn::SETTINGS_CONTACT_NUMBER_COL},
                        cn::SETTINGS_FAV_ICON_COL => isset($favIconFullPath) ? $favIconFullPath : null,
                        cn::SETTINGS_LOGO_IMAGE_COL => isset($logoImageFullPath) ? $logoImageFullPath : null,
                        cn::SETTINGS_SMTP_DRIVER_COL => $request->{cn::SETTINGS_SMTP_DRIVER_COL},
                        cn::SETTINGS_SMTP_HOST_COL => $request->{cn::SETTINGS_SMTP_HOST_COL},
                        cn::SETTINGS_SMTP_PORT_COL => $request->{cn::SETTINGS_SMTP_PORT_COL},
                        cn::SETTINGS_SMTP_USERNAME_COL => $request->{cn::SETTINGS_SMTP_USERNAME_COL},
                        cn::SETTINGS_SMTP_EMAIL_COL => $request->{cn::SETTINGS_SMTP_EMAIL_COL},
                        cn::SETTINGS_SMTP_PASSWORD_COL => $request->{cn::SETTINGS_SMTP_PASSWORD_COL},
                        cn::SETTINGS_SMTP_ENCRYPTION_COL => $request->{cn::SETTINGS_SMTP_ENCRYPTION_COL},
                    ]);
                }else{  // If first record is does not then create settings records
                    $UpdateSettings = Settings::create([
                        cn::SETTINGS_SITE_NAME_COL => $request->{cn::SETTINGS_SITE_NAME_COL},
                        cn::SETTINGS_SITE_URL_COL => $request->{cn::SETTINGS_SITE_URL_COL},
                        cn::SETTINGS_EMAIL_COL => $request->{cn::SETTINGS_EMAIL_COL},
                        cn::SETTINGS_CONTACT_NUMBER_COL => $request->{cn::SETTINGS_CONTACT_NUMBER_COL},
                        cn::SETTINGS_FAV_ICON_COL => isset($favIconImageName) ? $favIconFullPath : null,
                        cn::SETTINGS_LOGO_IMAGE_COL => isset($logoImageName) ? $logoImageFullPath : null,
                        cn::SETTINGS_SMTP_DRIVER_COL => $request->{cn::SETTINGS_SMTP_DRIVER_COL},
                        cn::SETTINGS_SMTP_HOST_COL => $request->{cn::SETTINGS_SMTP_HOST_COL},
                        cn::SETTINGS_SMTP_PORT_COL => $request->{cn::SETTINGS_SMTP_PORT_COL},
                        cn::SETTINGS_SMTP_USERNAME_COL => $request->{cn::SETTINGS_SMTP_USERNAME_COL},
                        cn::SETTINGS_SMTP_EMAIL_COL => $request->{cn::SETTINGS_SMTP_EMAIL_COL},
                        cn::SETTINGS_SMTP_PASSWORD_COL => $request->{cn::SETTINGS_SMTP_PASSWORD_COL},
                        cn::SETTINGS_SMTP_ENCRYPTION_COL => $request->{cn::SETTINGS_SMTP_ENCRYPTION_COL},
                    ]);
                    $this->StoreAuditLogFunction($request->all(),'Settings','','','Create Settings',cn::SETTINGS_TABLE_NAME,'');
                }
                // Check Setting are updated or not
                if($UpdateSettings){
                    return redirect('settings')->with('success_msg', __('languages.configuration_updated_successfully'));
                }else{
                    return back()->withInput()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }else{
                // Get setttings records
                $settingsData = [];
                $settingsData = Settings::first();
                return view('backend.settings.settings',compact('settingsData'));
            }
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }
}
