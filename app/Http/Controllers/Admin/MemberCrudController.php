<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MemberRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Member;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;



/**
 * Class MemberCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MemberCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Member::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/member');
        CRUD::setEntityNameStrings('member', 'members');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn(['name' => 'membership_number', 'label' => 'Membership No']);
        $this->crud->addColumn(['name' => 'first_name', 'label' => 'First Name']);
        $this->crud->addColumn(['name' => 'last_name', 'label' => 'Last Name']);
        $this->crud->addColumn(['name' => 'email', 'label' => 'Email']);
        $this->crud->addColumn(['name' => 'phone', 'label' => 'Phone']);
        $this->crud->addColumn(['name' => 'city', 'label' => 'City']);
        $this->crud->addColumn(['name' => 'state', 'label' => 'State']);
        $this->crud->addColumn(['name' => 'amount', 'label' => 'Amount Paid', 'type' => 'number', 'suffix' => ' $']);
        $this->crud->addColumn(['name' => 'payment_status', 'label' => 'Payment Status']);
        $this->crud->addColumn(['name' => 'created_at', 'label' => 'Registered At']);

        // Add the button to the top
        $this->crud->addButtonFromView('top', 'downloadTickets', 'download_tickets', 'beginning');
    }



    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(MemberRequest::class);

        $this->crud->addFields([
            [
                'name' => 'first_name',
                'label' => 'First Name',
                'type' => 'text',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'last_name',
                'label' => 'Last Name',
                'type' => 'text',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'email',
                'label' => 'Email Address',
                'type' => 'email',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'phone',
                'label' => 'Phone Number',
                'type' => 'text',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'address',
                'label' => 'Address',
                'type' => 'textarea',
                'wrapper' => ['class' => 'form-group col-md-12'],
            ],
            [
                'name' => 'city',
                'label' => 'City',
                'type' => 'text',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'state',
                'label' => 'State',
                'type' => 'text',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'referred_by',
                'label' => 'Referred By',
                'type' => 'text',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'referred_chapter_name',
                'label' => 'Referred Chapter',
                'type' => 'text',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'amount',
                'label' => 'Amount Paid ($)',
                'type' => 'number',
                'prefix' => '$',
                'attributes' => ['step' => '0.01'],
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'payment_status',
                'label' => 'Payment Status',
                'type' => 'select_from_array',
                'options' => [
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                ],
                'allows_null' => false,
                'default' => 'completed',
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'membership_number',
                'label' => 'Membership Number',
                'type' => 'text',
                'attributes' => ['readonly' => 'readonly'],
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
            [
                'name' => 'uuid',
                'label' => 'UUID',
                'type' => 'text',
                'attributes' => ['readonly' => 'readonly'],
                'wrapper' => ['class' => 'form-group col-md-6'],
            ],
        ]);
    }


    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function downloadTickets()
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $tickets = collect(Storage::disk('public')->files('raffle_cards'))
            ->filter(fn($f) => Str::endsWith($f, '.png'));

        $ticketGroups = $tickets->chunk(12); // 8 tickets per page

        $pdf = \PDF::loadView('admin.tickets_pdf', [
            'ticketGroups' => $ticketGroups
        ])->setPaper('a4', 'portrait');

        return $pdf->download('all-tickets.pdf');
    }
}
