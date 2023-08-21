<?php

namespace Database\Seeders;

use DB;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Actions']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'show',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'create',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'edit',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'delete',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Admin Dashboard']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_dashboard',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'General']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_general',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'region_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'group_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'branch_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'vendor_product_sourcing_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'vendor_servicing_partner_setting',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Product Management Settings']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_product_management_settings',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'product_category_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'brand_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'brand_model_setting',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Inventory / Stock']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_inventory',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_parts_receive',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_central_wareHouse_parts_stock',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_stock_in_hand',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'po_purchase_requistions',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Inventory Branch Requisitions']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_inventory_branch_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branches_all_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branches_allocated_requisition',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branches_re_allocated_requisition',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Inventory Branch Parts Return']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_central_branch_parts_return',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_central_branch_requested_parts_return',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_central_branch_received_parts_return',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Inventory / Stock Settings']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_inventory_settings',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'parts_setting',
            'guard_name' => 'web',
        ]);


        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'parts_category_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'price_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'parts_model_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'source_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'store_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'rack_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'bin_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'rack_bin_setting',
            'guard_name' => 'web',
        ]);
        

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Cash Sell']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_cash_sell',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_parts_sell',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Employee']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_employee',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_team_leader',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Employee Attendance']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_employee_attendance',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Employee Settings']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'designation_setting',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Technician']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_technician',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Technician Jobs']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_technician_jobs',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_technician_jobs_list',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_submitted_jobs',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_advance_payment',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Technicians Inventory']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_technicians_inventory',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_parts_return',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_all_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_allocated_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_received_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_technicians_stock',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Technicians Attendance']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_attendance',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Tickets']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_tickets',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_purchase',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_purchase_history',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_ticket_list',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_ticket_job_list',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_ticket_submited_job',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Tickets Settings']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_ticket_settings',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'fault_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'warranty_type_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'service_type_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'job_priority_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'product_condition_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'accessories_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'product_receive_mode_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'product_delivery_mode_setting',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Branch']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_parts_return',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_inventory',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Branch Requisitions']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_all_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_allocated_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_re_allocated_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_received_requisitions',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Branch Technicians Requisitions']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_all_technician_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_all_technician_all_requisitions',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_all_technician_allocated_requisitions',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Branch Technicians Parts Return']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_technicians_parts_return',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_requested_parts_return',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_received_parts_return',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Branch Parts Transfer']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_parts_transfer',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Branch Outgoing Parts Return']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_outgoing_parts_transfer',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_outgoing_parts_transfer_list',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_outgoing_parts_transfer_allocation_list',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_outgoing_parts_transfer_received_list',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Branch Incoming Parts Return']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_incoming_parts_transfer',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_incoming_parts_transfer_list',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_branch_incoming_parts_transfer_allocation_list',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Customer']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_customers',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Customer Settings']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_customer_settings',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'customer_grade_setting',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'feedback_question_setting',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Accounts']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_accounts',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_account_list',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_cash_transections',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_deposit',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_expense',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_revenue',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_cash_ledger',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Accounts Settings']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_account_settings',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'expense_item_setting',
            'guard_name' => 'web',
        ]);

        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Adminstrator']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_adminstrator',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_users',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_add_user',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_roles',
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_permission',
            'guard_name' => 'web',
        ]);

        //Reports
        $moduleAdminDashboard = Module::updateOrCreate(['name' => 'Reports']);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_reports',
            'guard_name' => 'web',
        ]);
        
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_job_reports',
            'guard_name' => 'web',
        ]);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_kpi_reports',
            'guard_name' => 'web',
        ]);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_consumption_reports',
            'guard_name' => 'web',
        ]);
        Permission::updateOrCreate([
            'module_id' => $moduleAdminDashboard->id,
            'name' => 'access_to_financial_reports',
            'guard_name' => 'web',
        ]);
    }
}
