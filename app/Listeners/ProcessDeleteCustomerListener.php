<?php

namespace App\Listeners;

use App\Events\ProcessDeleteCustomer;
use App\Models\Customer;
use App\Models\TemporaryCustomer;

class ProcessDeleteCustomerListener
{
    /**
     * Create the event listener.
     */
    public function __construct(public TemporaryCustomer $temporaryCustomerModel, public Customer $customerModel)
    {}

    /**
     * Handle the event.
     */
    public function handle(ProcessDeleteCustomer $event): void
    {
        $temCustomer = $this->temporaryCustomerModel->whereSlug($event->slug)->whereStatus('DELETE')->firstOrFail();
        $this->customerModel->whereSlug($temCustomer->slug)->delete();
        $temCustomer->delete();
    }
}
