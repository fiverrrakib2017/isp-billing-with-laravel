<?php
namespace App\Http\Controllers\Backend\Sms;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Message_template;
use App\Models\Pop_area;
use App\Models\Pop_branch;
use App\Models\Send_message;
use App\Models\Sms_configuration;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use function App\Helpers\send_message;

class SmsController extends Controller
{
    public function config()
    {
       $data= Sms_configuration::latest()->first();
        return view('Backend.Pages.Sms.Config',compact('data'));
    }
    public function sms_template_list()
    {

        return view('Backend.Pages.Sms.Template');
    }
    public function message_send_list()
    {
        return view('Backend.Pages.Sms.Send_list');
    }
    public function sms_template_get_all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'pop_id', 'name', 'message'];
        $orderByColumn = $request->order[0]['column'];
        $orderDirectection = $request->order[0]['dir'];

        $query = Message_template::with(['pop'])->when($search, function ($query) use ($search) {
            $query
                ->where('name', 'like', "%$search%")
                ->orWhere('message', 'like', "%$search%")
                ->orWhereHas('pop', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
        });

        $total = $query->count();

        $query = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirectection);

        $items = $query->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }
    public function sms_template_get($id){
        $data = Message_template::with(['pop'])->find($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
    public function config_store(Request $request)
    {
       /*Validate the form data*/
       $rules = [
        'api_url' => 'required|string',
        'api_key' => 'required|string',
        'sender_id' => 'required|string',
        'default_country_code' => 'required',
    ];
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json(
            [
                'success' => false,
                'errors' => $validator->errors(),
            ],
            422,
        );
    }

        /* Create a new Instance*/
        $object = Sms_configuration::firstOrNew([]);
        $object->api_url = $request->api_url;
        $object->api_key = $request->api_key;
        $object->sender_id = $request->sender_id;
        $object->default_country_code = $request->default_country_code;

        /* Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    }

    public function sms_template_Store(Request $request){

        /*Validate the form data*/
       $rules = [
        'pop_id' => 'required|integer',
        'name' => 'required|string',
        'message' => 'required|string',
    ];
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json(
            [
                'success' => false,
                'errors' => $validator->errors(),
            ],
            422,
        );
    }
     /* Create a new Instance*/
     $object =new Message_template();
     $object->pop_id = $request->pop_id;
     $object->name = $request->name;
     $object->message = $request->message;

     /* Save to the database table*/
     $object->save();
     return response()->json([
         'success' => true,
         'message' => 'Added successfully!',
     ]);

    }
    public function send_message_store(Request $request){
        /*Validate the form data*/
        $rules = [
            'customer_id' => 'required|integer',
            'message' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        /*Get POP ID From Customer table*/
        $customer=Customer::find($request->customer_id);
        /* Create a new Instance*/
        $object =new Send_message();
        $object->pop_id = $customer->pop_id;;
        $object->customer_id = $request->customer_id;
        $object->message = $request->message;
        $object->sent_at = Carbon::now();


         /*Call Send Message Function */
        send_message($customer->phone, $request->message);

        /* Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    }
    public function send_message_get_all_data(Request $request){
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'pop_id', 'name', 'message'];
        $orderByColumn = $request->order[0]['column'];
        $orderDirectection = $request->order[0]['dir'];

        $query = Send_message::with(['pop','customer'])->when($search, function ($query) use ($search) {
            $query
                ->where('message', 'like', "%$search%")
                // ->orWhere('message', 'like', "%$search%")
                ->orWhereHas('pop', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('fullname', 'like', "%$search%");
                    $query->where('username', 'like', "%$search%");
                });
        });

        $total = $query->count();

        $query = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirectection);

        $items = $query->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }
    public function sms_template_delete(Request $request)
    {
        $object = Message_template::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
    public function send_message_delete(Request $request){
        $object = Send_message::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

}
