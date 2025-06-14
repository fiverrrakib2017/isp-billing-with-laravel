<?php

namespace App\Console\Commands\Customer;
use RouterOS\Client;
use RouterOS\Query;
use App\Models\Customer;
use App\Models\Router;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\SessionService;

class check_expire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check_expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Customer Check Expire';

    /**
     * Execute the console command.
     */
    public function handle(SessionService $session_service)
    {
        $this->info('---Tasks Started ---');
        $this->check_expire_customer();

        /* Call the Session Remove method*/
        $session_service->forget_session_sidebar_customer();
        $this->info('---Tasks Finished ---');
    }
    protected function check_expire_customer()
    {
        $today = Carbon::now()->format('Y-m-d');

        $expire_customers = Customer::where('is_delete', '0')
            ->where('expire_date', '<', $today)
            ->whereIn('status', ['active', 'online', 'offline'])
            ->get();

        foreach ($expire_customers as $customer) {

            if ($customer->connection_type == 'pppoe') {
                $router = Router::where('status', 'active')->where('id', $customer->router_id)->first();
                if (!$router) {
                    $this->error("Router not found for customer {$customer->username}");
                    continue;
                }

                try {
                    $client = new Client([
                        'host' => $router->ip_address,
                        'user' => $router->username,
                        'pass' => $router->password,
                        'port' => (int) $router->port,
                        'timeout' => 3,
                        'attempts' => 1,
                    ]);

                    /* Find PPP secret*/
                    $query = new Query('/ppp/secret/print');
                    $query->where('name', $customer->username);
                    $secrets = $client->query($query)->read();

                    if (!empty($secrets)) {
                        $secretId = $secrets[0]['.id'];

                        /* Remove from active list*/
                        $removeActive = new Query('/ppp/active/remove');
                        $removeActive->equal('name', $customer->username);
                        $client->query($removeActive)->read();

                        /*Disable the secret*/
                        $disableSecret = new Query('/ppp/secret/set');
                        $disableSecret->equal('.id', $secretId)->equal('disabled', 'yes');
                        $client->query($disableSecret)->read();

                        $this->info("MikroTik: Customer {$customer->username} is now DISABLED & REMOVED.");
                    } else {
                        $this->warn("MikroTik: PPP secret not found for {$customer->username}");
                    }

                    /* Now update DB*/
                    $customer->update(['status' => 'expired']);
                    $this->info("Customer {$customer->username} is (Expired)");
                } catch (\Exception $e) {
                    $this->error("Router connection failed for {$customer->username}: " . $e->getMessage());
                }
            }
            if($customer->connection_type == 'radius'){
                \App\Models\Radius\Radcheck::where('username', $customer->username)->delete();
                \App\Models\Radius\Radreply::where('username', $customer->username)->delete();

                $this->info("Radius user {$customer->username} access removed.");
                /* Now update DB*/
                $customer->update(['status' => 'expired']);
                $this->info("Customer {$customer->username} is (Expired)");
            }
            if($customer->connection_type=='hotspot'){

            }

        }
    }
}
