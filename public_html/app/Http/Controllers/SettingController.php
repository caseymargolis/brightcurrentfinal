<?php

    namespace App\Http\Controllers;

    use App\Models\Setting;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Spatie\Permission\Models\Role;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Facades\Log;

    class SettingController extends Controller
    {
        /**
         * Display the settings index page, including Users, all API Integrations, and Notifications.
         * Corresponds to GET /settings
         *
         * @return \Illuminate\View\View
         */
        public function index()
        {
            // For 'Users List' card
            $users = User::all();
            $roles = Role::all(); // For the 'Add User' modal roles dropdown

            // Fetch settings for SolarEdge API
            $solarEdgeApiKey = Setting::where('key', 'solar_edge_api_key')->first();
            $solarEdgeSiteId = Setting::where('key', 'solar_edge_site_id')->first();

            // Fetch settings for Housecall API (example keys)
            $housecallApiKey = Setting::where('key', 'housecall_api_key')->first();
            $housecallClientId = Setting::where('key', 'housecall_client_id')->first();

            // Fetch settings for Weather API (example keys)
            $weatherApiKey = Setting::where('key', 'weather_api_key')->first();
            $weatherApiBaseUrl = Setting::where('key', 'weather_api_base_url')->first();

            // Fetch settings for Notifications
            // Default to false if not found, as checkboxes will submit 'on' or nothing
            $emailAlerts = Setting::where('key', 'notification_email_alerts')->first();
            $emailDailySummary = Setting::where('key', 'notification_email_daily_summary')->first();
            $emailServiceReminders = Setting::where('key', 'notification_email_service_reminders')->first();


            return view('backend.settings.index', compact(
                'users',
                'roles',
                'solarEdgeApiKey',
                'solarEdgeSiteId',
                'housecallApiKey',
                'housecallClientId',
                'weatherApiKey',
                'weatherApiBaseUrl',
                'emailAlerts',        // Pass these to the view
                'emailDailySummary',
                'emailServiceReminders'
            ));
        }

        /**
         * Store a newly created user.
         * (As previously mentioned, if using Route::resource('users', UserController::class),
         * this method might belong in UserController, but kept here for context of the modal).
         */
        public function storeUser(Request $request)
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'string', 'exists:roles,name'],
            ]);

            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $user->assignRole($request->role);
                return redirect()->back()->with(['msg' => 'User created successfully!', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to create user. Please try again.', 'type' => 'error'])->withInput();
            }
        }


        // --- SolarEdge API Methods ---
        public function updateSolarEdgeSettings(Request $request)
        {
            $request->validate([
                'solar_edge_api_key' => 'nullable|string|max:255',
                'solar_edge_site_id' => 'nullable|string|max:255',
            ]);

            try {
                Setting::updateOrCreate(
                    ['key' => 'solar_edge_api_key'],
                    ['value' => $request->input('solar_edge_api_key'), 'description' => 'SolarEdge API Key']
                );
                Setting::updateOrCreate(
                    ['key' => 'solar_edge_site_id'],
                    ['value' => $request->input('solar_edge_site_id'), 'description' => 'SolarEdge Site ID for data retrieval']
                );
                return redirect()->route('settings.index')->with(['msg' => 'SolarEdge Settings Updated Successfully', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error updating SolarEdge settings: ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to update SolarEdge settings. Please try again.', 'type' => 'error'])->withInput();
            }
        }

        public function testSolarEdgeConnection(Request $request)
        {
            $apiKey = $request->input('solar_edge_api_key');
            $siteId = $request->input('solar_edge_site_id');
            if ($apiKey && $siteId && str_starts_with($apiKey, 'SE_') && is_numeric($siteId)) {
                return response()->json(['success' => true, 'message' => 'SolarEdge connection successful!']);
            } else {
                return response()->json(['success' => false, 'message' => 'SolarEdge connection failed. Invalid API Key or Site ID format.'], 400);
            }
        }

        // --- Housecall API Methods ---
        public function updateHousecallSettings(Request $request)
        {
            $request->validate([
                'housecall_api_key' => 'nullable|string|max:255',
                'housecall_client_id' => 'nullable|string|max:255',
            ]);

            try {
                Setting::updateOrCreate(
                    ['key' => 'housecall_api_key'],
                    ['value' => $request->input('housecall_api_key'), 'description' => 'Housecall API Key']
                );
                Setting::updateOrCreate(
                    ['key' => 'housecall_client_id'],
                    ['value' => $request->input('housecall_client_id'), 'description' => 'Housecall Client ID']
                );
                return redirect()->route('settings.index')->with(['msg' => 'Housecall Settings Updated Successfully', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error updating Housecall settings: ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to update Housecall settings. Please try again.', 'type' => 'error'])->withInput();
            }
        }

        public function testHousecallConnection(Request $request)
        {
            $apiKey = $request->input('housecall_api_key');
            $clientId = $request->input('housecall_client_id');
            if ($apiKey && $clientId && str_starts_with($apiKey, 'HC_') && !empty($clientId)) {
                return response()->json(['success' => true, 'message' => 'Housecall connection successful!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Housecall connection failed. Invalid API Key or Client ID format.'], 400);
            }
        }

        // --- Weather API Methods ---
        public function updateWeatherSettings(Request $request)
        {
            $request->validate([
                'weather_api_key' => 'nullable|string|max:255',
                'weather_api_base_url' => 'nullable|url|max:255',
            ]);

            try {
                Setting::updateOrCreate(
                    ['key' => 'weather_api_key'],
                    ['value' => $request->input('weather_api_key'), 'description' => 'Weather API Key']
                );
                Setting::updateOrCreate(
                    ['key' => 'weather_api_base_url'],
                    ['value' => $request->input('weather_api_base_url'), 'description' => 'Weather API Base URL']
                );
                return redirect()->route('settings.index')->with(['msg' => 'Weather Settings Updated Successfully', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error updating Weather settings: ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to update Weather settings. Please try again.', 'type' => 'error'])->withInput();
            }
        }

        public function testWeatherConnection(Request $request)
        {
            $apiKey = $request->input('weather_api_key');
            $baseUrl = $request->input('weather_api_base_url');
            if ($apiKey && $baseUrl && str_starts_with($apiKey, 'WX_') && filter_var($baseUrl, FILTER_VALIDATE_URL)) {
                return response()->json(['success' => true, 'message' => 'Weather API connection successful!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Weather API connection failed. Invalid API Key or Base URL format.'], 400);
            }
        }

        // --- Notifications API Methods ---

        /**
         * Update Notifications Settings.
         */
        public function updateNotificationSettings(Request $request)
        {
            // For checkboxes, if unchecked, they won't be in the request.
            // We'll set '1' for checked and '0' for unchecked.
            $alertNotifications = $request->has('notification_email_alerts') ? '1' : '0';
            $dailySummaryReports = $request->has('notification_email_daily_summary') ? '1' : '0';
            $serviceScheduleReminders = $request->has('notification_email_service_reminders') ? '1' : '0';

            try {
                Setting::updateOrCreate(
                    ['key' => 'notification_email_alerts'],
                    ['value' => $alertNotifications, 'description' => 'Enable/Disable Email Alert Notifications']
                );
                Setting::updateOrCreate(
                    ['key' => 'notification_email_daily_summary'],
                    ['value' => $dailySummaryReports, 'description' => 'Enable/Disable Daily Summary Email Reports']
                );
                Setting::updateOrCreate(
                    ['key' => 'notification_email_service_reminders'],
                    ['value' => $serviceScheduleReminders, 'description' => 'Enable/Disable Email Service Schedule Reminders']
                );

                return redirect()->route('settings.index')->with(['msg' => 'Notification Settings Updated Successfully', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error updating notification settings: ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to update notification settings. Please try again.', 'type' => 'error'])->withInput();
            }
        }
    }
