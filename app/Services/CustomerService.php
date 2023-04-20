<?php

namespace App\Services;

use App\Models\User;
use App\Models\Customer;
use App\Helpers\SystemHelper;
use App\Traits\AuthUserTrait;
use App\Models\TemporaryCustomer;
use Illuminate\Http\JsonResponse;
use App\Helpers\ActivityLogHelper;
use App\Events\ProcessCreateCustomer;
use App\Events\ProcessDeleteCustomer;
use App\Events\ProcessUpdateCustomer;
use Illuminate\Support\Facades\Event;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyAdminNotification;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\TemporaryCustomerResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Customer service
 */
class CustomerService
{

    use AuthUserTrait;

    /**
     * @param Customer $customerModel
     * @param TemporaryCustomer $temporaryCustomerModel
     * @param SystemHelper $systemHelper
     */
    public function __construct(public Customer $customerModel, public TemporaryCustomer $temporaryCustomerModel, public  SystemHelper $systemHelper, public ActivityLogHelper $activityLogHelper, public User $userModel)
    {}

    /**
     * @param $request
     * @return JsonResponse
     */
    public function createCustomer($request): JsonResponse
    {
        try {
            $customer = $this->temporaryCustomerModel->create([
                'user_id' => $this->getAuthUser()->id,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email'=> $request->email,
                'slug' => $this->systemHelper->systemSlugHelper($request->firstName),
                'status' => 'CREATE'
            ]);
            $this->activityLogHelper->log($this->getAuthUser()->id, 'Create Customer Request', 'Createad a new user', 'Successful'); //create activity log
            $this->notifyAdmin('create');
            return response()->json([
                'message' => 'Customer account created successfully',
                'data' => new TemporaryCustomerResource($customer),
                'success' => true
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->activityLogHelper->log($this->getAuthUser()->id, 'Create Customer Request', 'Createad a new user', 'Failed'); //create activity log
            return response()->json([
                'message' => 'Sorry unable to create customer',
                'error' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getAllCustomers(): AnonymousResourceCollection
    {
        $customers = $this->customerModel->latest()->paginate(20);
        return CustomerResource::collection($customers)->additional([
            'message' => "All customers fetched successfully",
            'success' => true
        ], Response::HTTP_OK);
    }

    /**
     * @param $slug
     * @return CustomerResource
     */
    public function getSingleCustomer($slug): CustomerResource
    {
        $customer = $this->customerModel->whereSlug($slug)->firstOrFail();
        return (new CustomerResource($customer))->additional( [
            'message' => "Customer details fetch successfully",
            'success' => true
        ], Response::HTTP_OK);
    }

    /**
     * @param $request
     * @param $slug
     * @return JsonResponse
     */
    public function updateCustomer($request, $slug): JsonResponse
    {
        $customer = $this->customerModel->whereSlug($slug)->firstOrFail();
        try {
            $customer = $this->temporaryCustomerModel->create([
                'user_id' => $this->getAuthUser()->id,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email'=> $request->email,
                'slug' => $slug,
                'status' => 'UPDATE'
            ]);
            $this->activityLogHelper->log($this->getAuthUser()->id, 'Update Customer Request', 'Update customer details', 'Successful'); //create activity log
            $this->notifyAdmin('update');
            return response()->json([
                'message' => 'Customer account  update request made successfully',
                'data' => new CustomerResource($customer),
                'success' => true
            ], Response::HTTP_OK);
        }  catch (\Exception $e) {
            $this->activityLogHelper->log($this->getAuthUser()->id, 'Update Customer Request', 'Update customer details', 'Failed'); //create activity log
            return response()->json([
                'message' => 'Sorry unable to update customer',
                'error' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function deleteCustomer($slug): JsonResponse
    {
        $customer = $this->customerModel->whereSlug($slug)->firstOrFail();
        try {
            $customer = $this->temporaryCustomerModel->create([
                'user_id' => $this->getAuthUser()->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email'=> $customer->email,
                'slug' => $customer->slug,
                'status' => 'DELETE'
            ]);
            $this->activityLogHelper->log($this->getAuthUser()->id, 'Delete Customer Request', 'Delete customer details', 'Successful'); //create activity log
            $this->notifyAdmin('delete');
            return response()->json([
                'message' => 'Customer delete request made successfully',
                'data' => new CustomerResource($customer),
                'success' => true
            ], Response::HTTP_OK);
        }  catch (\Exception $e) {
            $this->activityLogHelper->log($this->getAuthUser()->id, 'Delete Customer Request', 'Delete customer details', 'Failed'); //create activity log
            return response()->json([
                'message' => 'Sorry unable to delete',
                'error' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $request
     * @param $slug
     * @return JsonResponse
     */
    public function approveCustomerRequest($request, $slug): JsonResponse
    {
        $temCustomer =  $this->temporaryCustomerModel->whereSlug($slug)->firstOrFail();
        if($temCustomer->user_id != $this->getAuthUser()->id) {
            if ($request->approval_status == 'approved') {
                $operation = $request->operation;
                $return_value = match ($operation) {
                    'create' => Event::dispatch(new  ProcessCreateCustomer($slug)),
                    'update' => Event::dispatch(new  ProcessUpdateCustomer($slug)),
                    'delete' => Event::dispatch(new  ProcessDeleteCustomer($slug)),
                };
                $this->activityLogHelper->log($this->getAuthUser()->id, 'Approve request', 'Approve request', 'Successful'); //create activity log
                return response()->json([
                    'message' => 'Pending request approved successfully',
                    'success' => true
                ], Response::HTTP_OK);
            } else {
                $this->activityLogHelper->log($this->getAuthUser()->id, 'Approve request', 'Approve request', 'Failed'); //create activity log
                $this->temporaryCustomerModel->whereSlug($slug)->update([
                    'approval_status' => 'CANCELED'
                ]);
                return response()->json([
                    'message' => 'Request cancled due to some resons',
                    'success' => true
                ], Response::HTTP_OK);
            }
        } else {
            $this->activityLogHelper->log($this->getAuthUser()->id, 'Approve request', 'Approve request', 'Failed'); //create activity log
            return response()->json([
                'message' => 'Sorry you can not perform this action cause you created it',
                'success' => false
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getAllTemporaryRequest(): AnonymousResourceCollection
    {
        $customers = $this->temporaryCustomerModel->latest()->paginate(20);
        return TemporaryCustomerResource::collection($customers)->additional([
            'message' => "All customers request fetched successfully",
            'success' => true
        ], Response::HTTP_OK);
    }

    /**
     * @param $requestData
     * @return void
     */
    public function notifyAdmin($requestData): void
    {
        $admins = User::where('id', '!=', $this->getAuthUser()->id)->get();
        Notification::send($admins, new NotifyAdminNotification($requestData));
    }

}
