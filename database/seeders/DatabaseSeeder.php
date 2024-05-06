<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ApiToken;
use App\Models\AppSettings;
use App\Models\BillerList;
use App\Models\Countries;
use App\Models\PaymentSettings;
use App\Models\Policy;
use App\Models\SMSSettings;
use App\Models\User;
use App\Providers\AppFunctionProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * List of applications to add.
     */
    private array $permissions = [
        'Can View Roles',
        'Can Create Roles',
        'Can Edit Roles',
        'Can Delete Roles',
        'Can View Permissions',
        'Can Change Permissions',
        'Can View Users',
        'Can Create Users',
        'Can Edit Users',
        'Can Delete Users',
        'Can View Biller Groups',
        'Can Create Biller Groups',
        'Can Edit Biller Groups',
        'Can Delete Biller Groups',
        'Can View Biller Lists',
        'Can Create Biller Lists',
        'Can Edit Biller Lists',
        'Can Delete Biller Lists',
        'Can Change Settings',
        'Can Change Payment Settings',
        'Can View App Category',
        'Can Create App Category',
        'Can Edit App Category',
        'Can Delete App Category',
        'Can View Transactions',
        'Can View Merchants',
        'Can Create Merchants',
        'Can Edit Merchants',
        'Can Delete Merchants',
        'Can Change SMS Settings',
        'Can Change API Key',
        'Can View App Banners',
        'Can Create App Banners',
        'Can Edit App Banners',
        'Can Delete App Banners',
        'Can Change Policy',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin_role = Role::findOrCreate('Admin');
        $admin_role->is_admin_login_allowed = true;
        $admin_role->save();

        Role::findOrCreate('User');

        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $permissions = Permission::pluck('id', 'id')->all();

        $admin_role->syncPermissions($permissions);

        $biller_list_file_path = public_path('storage/defaults/' . 'Biller-List.csv');
        $csv_data = AppFunctionProvider::readCSV($biller_list_file_path);
        foreach ($csv_data as $biller) {
            $biller_check = BillerList::where(['domain_code' => $biller[0], 'biller_name' => $biller[2]])->exists();
            if (!$biller_check) {
                BillerList::create([
                    'domain_code' => $biller[0],
                    'biller_name' => $biller[2],
                    'biller_category' => $biller[3],
                    'availability' => $biller[4],
                    'transaction_type' => $biller[5]
                ]);
            }
        }

        $country_list = AppFunctionProvider::readJSON(public_path('storage/defaults/' . 'countries.json'));
        foreach ($country_list as $country) {
            if (!Countries::where('name', $country['name'])->exists()) {
                Countries::create([
                    'name' => $country['name'],
                    'code' => $country['code']
                ]);
            }
        }

        $app_settings = AppSettings::where(['codename' => 'superapp']);
        if (!$app_settings->exists()) {
            AppSettings::create([
                'codename' => 'superapp'
            ]);
        }

        $payment_settings = PaymentSettings::where(['codename' => 'superapp']);
        if (!$payment_settings->exists()) {
            PaymentSettings::create([
                'codename' => 'superapp'
            ]);
        }

        $api_token = ApiToken::where(['codename' => 'superapp']);
        if (!$api_token->exists()) {
            ApiToken::create([
                'codename' => 'superapp',
                'api_key' => Str::orderedUuid()->toString()
            ]);
        }

        $sms_settings = SMSSettings::where(['codename' => 'superapp']);
        if (!$sms_settings->exists()) {
            SMSSettings::create([
                'codename' => 'superapp'
            ]);
        }

        $policy = Policy::where(['key' => 'terms-and-conditions']);
        if (!$policy->exists()) {
            Policy::create([
                'title' => 'Terms & Conditions',
                'key' => 'terms-and-conditions'
            ]);
        }

        $policy = Policy::where(['key' => 'privacy-policy']);
        if (!$policy->exists()) {
            Policy::create([
                'title' => 'Privacy Policy',
                'key' => 'privacy-policy'
            ]);
        }

        $admin_email = 'admin@webxpay.com';
        $admin_mobile = '94000000000';
        if (!User::where('email', $admin_email)->where('mobile', $admin_mobile)->exists()) {
            $admin_user = User::create([
                'first_name' => 'Webxpay',
                'mobile' => $admin_mobile,
                'email' => $admin_email,
                'password' => 'Admin@2020',
                'is_active' => true,
                'is_mobile_verified' => true
            ]);
            $admin_user->assignRole('Admin');
        }
    }
}
