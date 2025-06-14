<?php
namespace App\Http\Controllers\Backend\Router;
use App\Http\Controllers\Controller;
use App\Models\Pop_branch;
use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use RouterOS\Client;
use RouterOS\Query;
use Carbon\Carbon;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::where('status', 'active')->get();

        $mikrotik_data = [];

        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int) $router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);


                $query = new Query('/ppp/active/print');
                $activeUsers = $client->query($query)->read();


                $resourceQuery = new Query('/system/resource/print');
                $resourceDetails = $client->query($resourceQuery)->read();

                $mikrotik_data[] = [
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'online_users' => count($activeUsers),
                    'uptime' => $resourceDetails[0]['uptime'] ?? 'N/A',
                    'version' => $resourceDetails[0]['version'] ?? 'N/A',
                    'hardware' => $resourceDetails[0]['hardware'] ?? 'N/A',
                    'cpu' => $resourceDetails[0]['cpu'] ?? 'N/A',
                    'offline_users' => 0,
                ];
            } catch (\Exception $e) {
                $mikrotik_data[] = [
                    'error' => $e->getMessage()
                ];
            }
        }
        $mikrotik_data = collect($mikrotik_data);
        // return $mikrotik_data;
        return view('Backend.Pages.Router.index', compact('routers', 'mikrotik_data'));
    }



    public function store(Request $request)
    {
        /* Validate the form data*/
        $this->validateForm($request);

        /* Create a new Supplier*/
        $object = new Router();
        $object->name = $request->name;
        $object->pop_id = $request->pop_id;
        $object->ip_address = $request->ip_address;
        $object->username = $request->username;
        $object->password = $request->password;
        $object->port = $request->port;
        $object->status = $request->status;
        $object->api_version = $request->api_version;
        $object->location = $request->location;
        $object->remarks = $request->remarks;

        /* Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!'
        ]);
    }
    public function router_log(){
        $routers=Router::where('status', 'active')->get();
        $allLogs = [];
        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int)$router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);

                $query = new Query('/log/print');
                $logs = $client->query($query)->read();

                foreach ($logs as $log) {
                    $allLogs[] = [
                        'router_name' => $router->name,
                        'message'     => $log['message'] ?? '',
                        'time'        =>  Carbon::parse($log['time'])->format('l, F j, Y g:i A') ?? '',
                        'topics'      => $log['topics'] ?? '',
                    ];
                }
            } catch (\Exception $e) {
                $allLogs[] = [
                    'router_name' => $router->name,
                    'message'     => 'Connection failed: ' . $e->getMessage(),
                    'time'        => now(),
                    'topics'      => 'error'
                ];
            }
        }
        return view('Backend.Pages.Mikrotik.log', compact('allLogs'));
    }

    public function router_user_list($router_id){
        $routers=Router::where('status', 'active')->where('id',$router_id)->get();
        $data = [];
        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int)$router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);

                $query = new Query('/ppp/active/print');
                $data = $client->query($query)->read();
                // return $data;
                return view('Backend.Pages.Mikrotik.customers', compact('data'));
            } catch (\Exception $e) {
                $data[] = [
                    'router_name' => $router->name,
                    'message'     => 'Connection failed: ' . $e->getMessage(),
                    'time'        => now(),
                    'topics'      => 'error'
                ];
            }
        }
        // return $data;
        // return view('Backend.Pages.Mikrotik.customers',  ['data' => $data]);
    }


    public function delete(Request $request)
    {
        $object = Router::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' =>true, 'message'=> 'Deleted successfully.']);
    }
    public function edit($id)
    {
        $data = Router::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit;
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }

    public function update(Request $request, $id){
        $this->validateForm($request);

        $object = Router::findOrFail($id);
        $object->name = $request->name;
        $object->pop_id = $request->pop_id;
        $object->ip_address = $request->ip_address;
        $object->username = $request->username;
        $object->password = $request->password;
        $object->port = $request->port;
        $object->status = $request->status;
        $object->api_version = $request->api_version;
        $object->location = $request->location;
        $object->remarks = $request->remarks;
        $object->update();

        return response()->json([
            'success' => true,
            'message' => 'Update successfully!',
        ]);
    }



    private function validateForm($request)
    {

        /*Validate the form data*/
        $rules=[
            'name' => 'required|string|max:100',
            'ip_address' => 'required|ip',
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:100',
            'port' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    }
}
