<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer controller
 */
class CustomerController extends Controller
{

    /**
     * @param CustomerService $customerService
     */
    public function __construct(public CustomerService $customerService)
    {}

    /**
     * get all customers
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
       return  $this->customerService->getAllCustomers();
    }

    /**
     * create customers
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'firstName' => 'required',
            'lastName' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->first(),
                'success' => false
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }else {
            return  $this->customerService->createCustomer($request);
        }
    }

    /**
     * get single customer
     * @param string $slug
     * @return CustomerResource
     */
    public function show(string $slug): CustomerResource
    {
       return  $this->customerService->getSingleCustomer($slug);
    }

    /**
     * update customer
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function update(Request $request, string $slug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes',
            'firstName' => 'sometimes',
            'lastName' => 'sometimes'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->first(),
                'success' => false
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }else {
            return  $this->customerService->updateCustomer($request, $slug);
        }
    }

    /**
     * delete customer
     * @param string $slug
     * @return JsonResponse
     */
    public function destroy(string $slug): JsonResponse
    {
        return  $this->customerService->deleteCustomer($slug);
    }

    /**
     * approve customer
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function approve(Request $request, string $slug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'approval_status' => 'required',
            'operation'  => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->first(),
                'success' => false
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }else {
           return  $this->customerService->approveCustomerRequest($request, $slug);
        }
    }

    /**
     * get all temporary customer request
     * @return AnonymousResourceCollection
     */
    public function getTemporaryCustomersRequest(): AnonymousResourceCollection
    {
       return  $this->customerService->getAllTemporaryRequest();
    }

}
