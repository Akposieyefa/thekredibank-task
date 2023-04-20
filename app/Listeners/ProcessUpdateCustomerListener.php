<?php

namespace App\Listeners;

use App\Events\ProcessUpdateCustomer;
use App\Models\Customer;
use App\Models\TemporaryCustomer;

class ProcessUpdateCustomerListener
{
    /**
     * Create the event listener.
     */
    public function __construct(public TemporaryCustomer $temporaryCustomerModel, public Customer $customerModel)
    {}

    /**
     * Handle the event.
     */
    public function handle(ProcessUpdateCustomer $event): void
    {
        $temCustomer = $this->temporaryCustomerModel->whereSlug($event->slug)->whereStatus('UPDATE')->firstOrFail();
        $this->customerModel->whereSlug($temCustomer->slug)->update([
            'first_name' => $temCustomer->first_name,
            'last_name' => $temCustomer->last_name,
            'email' => $temCustomer->email
        ]);
        $temCustomer->delete();
    }
}
