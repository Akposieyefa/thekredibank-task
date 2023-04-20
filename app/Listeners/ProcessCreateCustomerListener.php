<?php

namespace App\Listeners;

use App\Events\ProcessCreateCustomer;
use App\Models\Customer;
use App\Models\TemporaryCustomer;

class ProcessCreateCustomerListener
{
    /**
     * Create the event listener.
     */
    public function __construct(public TemporaryCustomer $temporaryCustomerModel, public Customer $customerModel)
    {}

    /**
     * Handle the event.
     */
    public function handle(ProcessCreateCustomer $event): void
    {
        $temCustomer = $this->temporaryCustomerModel->whereSlug($event->slug)->whereStatus('CREATE')->firstOrFail();
        $this->customerModel->create([
            'user_id' => $temCustomer->user_id,
            'first_name' => $temCustomer->first_name,
            'last_name' => $temCustomer->last_name,
            'email' => $temCustomer->email,
        ]);
        $temCustomer->delete();
    }
}
