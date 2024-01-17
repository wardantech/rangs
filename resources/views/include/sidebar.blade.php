<div class="app-sidebar colored">
    <div class="sidebar-header">
        <a class="header-brand" href="{{ route('dashboard') }}">
            <div class="logo-img">
                <img height="26px" src="{{ asset('img/logo2.webp') }}" class="header-brand-img" title="Rangs">
            </div>
        </a>
        {{-- <div class="sidebar-action"><i class="ik ik-arrow-left-circle"></i></div>
        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button> --}}
    </div>

    @php
        $segment1 = request()->segment(1);
        $segment2 = request()->segment(2);
    @endphp

    <div class="sidebar-content">
        <div class="nav-container">
            <nav id="main-menu-navigation" class="navigation-main">
                <div class="nav-item {{ $segment1 == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i
                            class="ik ik-bar-chart-2"></i><span>{{ __('Dashboard') }}</span></a>
                </div>
                {{-- General --}}
                @canany(['access_to_general', 'region_setting', 'group_setting', 'branch_setting',
                    'vendor_product_sourcing_setting', 'vendor_servicing_partner_setting'])
                    <div
                        class=" nav-item {{ \Request::is('general/*') || \Request::is('general') ? 'active open' : '' }} has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-briefcase"></i><span>General</span></a>
                        <div class="submenu-content">
                            <div
                                class="nav-item{{ \Request::is('general/region/*') || \Request::is('general/region') || \Request::is('general/group/*') || \Request::is('general/group') || \Request::is('general/outlet/*') || \Request::is('general/outlet') || \Request::is('general/product-sourcing-vendor') || \Request::is('general/product-sourcing-vendor/*') || \Request::is('general/service-sourcing-vendor') || \Request::is('general/service-sourcing-vendor/*') ? 'active open' : '' }} has-sub">
                                @canany(['access_to_general', 'region_setting', 'group_setting', 'branch_setting',
                                    'vendor_product_sourcing_setting', 'vendor_servicing_partner_setting'])
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('general/region/*') || \Request::is('general/region') || \Request::is('general/group/*') || \Request::is('general/group') || \Request::is('general/outlet/*') || \Request::is('general/outlet') || \Request::is('general/product-sourcing-vendor') || \Request::is('general/product-sourcing-vendor/*') || \Request::is('general/service-sourcing-vendor') || \Request::is('general/service-sourcing-vendor/*') ? 'active open' : '' }}">Settings</a>
                                @endcan
                                <div class="submenu-content">
                                    @can('region_setting')
                                        <a href="{{ url('general/region') }}"
                                            class="menu-item {{ \Request::is('general/region') || \Request::is('general/region/*') ? 'active open' : '' }}">Region</a>
                                    @endcan
                                    @can('group_setting')
                                        <a href="{{ url('general/group') }}"
                                            class="menu-item {{ \Request::is('general/group') || \Request::is('general/group/*') ? 'active open' : '' }}">Group</a>
                                    @endcan
                                    @can('branch_setting')
                                        <a href="{{ url('general/outlet') }}"
                                            class="menu-item {{ \Request::is('general/outlet') || \Request::is('general/outlet/*') ? 'active open' : '' }}">Branch</a>
                                    @endcan
                                    @can('vendor_product_sourcing_setting')
                                        <a href="{{ url('general/product-sourcing-vendor') }}"
                                            class="menu-item {{ \Request::is('general/product-sourcing-vendor') || \Request::is('general/product-sourcing-vendor/*') ? 'active open' : '' }}">Vendor
                                            Product Sourcing</a>
                                    @endcan
                                    @can('vendor_servicing_partner_setting')
                                        <a href="{{ url('general/service-sourcing-vendor') }}"
                                            class="menu-item {{ \Request::is('general/service-sourcing-vendor') || \Request::is('general/service-sourcing-vendor/*') ? 'active open' : '' }}">Vendor
                                            Servicing Partner</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                {{-- Prduct Purchase --}}
                @canany(['access_to_product_management_settings', 'product_category_setting', 'brand_setting',
                    'brand_model_setting'])
                    <div
                        class=" nav-item {{ \Request::is('product/*') || \Request::is('product') ? 'active open' : '' }} has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-list"></i><span>Product Management</span></a>
                        <div class="submenu-content">
                            <div
                                class="nav-item {{ \Request::is('product/category') || \Request::is('product/brand/*') || \Request::is('product/brand') || \Request::is('product/brand_model/*') || \Request::is('product/brand_model') ? 'active open' : '' }} has-sub">

                                @canany(['access_to_product_management_settings', 'product_category_setting',
                                    'brand_setting', 'brand_model_setting'])
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('product/category') || \Request::is('product/brand') || \Request::is('product/edit/*') || \Request::is('product/brand_model/*') || \Request::is('product/brand_model') || \Request::is('product/brand/*') ? 'active open' : '' }}">Settings</a>
                                @endcan

                                <div class="submenu-content">
                                    @can('product_category_setting')
                                        <a href="{{ url('product/category') }}"
                                            class="menu-item {{ \Request::is('product/category') ? 'active open' : '' }}">Product
                                            Category</a>
                                    @endcan

                                    @can('brand_setting')
                                        <a href="{{ route('product.brand.index') }}"
                                            class="menu-item {{ \Request::is('product/brand') || \Request::is('product/brand/*') ? 'active open' : '' }}">Brand</a>
                                    @endcan

                                    @can('brand_model_setting')
                                        <a href="{{ url('product/brand_model') }}"
                                            class="menu-item {{ \Request::is('product/brand_model') || \Request::is('product/brand_model/*') ? 'active open' : '' }}">Brand
                                            Model</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                {{-- Inventory --}}
                @canany(['access_to_inventory', 'access_to_parts_receive', 'access_to_central_wareHouse_parts_stock',
                    'access_to_stock_in_hand', 'po_purchase_requistions', 'access_to_central_branch_parts_return',
                    'access_to_central_branch_requested_parts_return', 'access_to_central_branch_received_parts_return',
                    'access_to_inventory_branch_requisitions', 'access_to_branches_all_requisitions',
                    'access_to_branches_allocated_requisition', 'access_to_branches_re_allocated_requisition',
                    'access_to_inventory_settings', 'parts_setting', 'parts_category_setting', 'price_setting',
                    'parts_model_setting', 'source_setting', 'store_setting', 'rack_setting', 'bin_setting'])
                    <div
                        class=" nav-item {{ \Request::is('inventory/create') ||\Request::is('inventory/show/*') ||\Request::is('inventory/edit') ||\Request::is('inventory/stock') ||\Request::is('inventory/show-inventory-details/*') ||\Request::is('inventory/stock-in-hand') ||\Request::is('inventory/stock-in-hand-all') ||\Request::is('inventory/part-category') ||\Request::is('inventory/parts') ||\Request::is('inventory/parts/*') ||\Request::is('inventory/price-management') ||\Request::is('inventory/price-management/*') ||\Request::is('inventory/parts_model') ||\Request::is('inventory/parts_model/*') ||\Request::is('inventory/source') ||\Request::is('inventory/source/*') ||\Request::is('inventory/store/*') ||\Request::is('inventory/store') ||\Request::is('inventory/racks') ||\Request::is('inventory/bins') ||\Request::is('inventory/bins/*') ||\Request::is('inventory/racks/*') ||\Request::is('inventory') ||\Request::is('central/requisitions/*') ||\Request::is('central/requisitions') ||\Request::is('central/re-allocation') ||\Request::is('central/re-allocation/edit/*') ||\Request::is('central/re-allocations') ||\Request::is('central/re-allocation/show/*') ||\Request::is('central/requisitions/allocation/*') ||\Request::is('central/requisitions/allocation') ||\Request::is('central/branch-parts-return/*') ||\Request::is('central/branch-parts-return') ||\Request::is('purchase/requisitions/*') ||\Request::is('purchase/requisitions') ||\Request::is('central/branch-parts-return/received/*') ||\Request::is('central/branch-parts-return/received') ||\Request::is('inventory/rack-bin-management/*') ||\Request::is('inventory/rack-bin-management') ||\Request::is('central/requisition-item') || \Request::is('central/requisitions/allocation-item') || \Request::is('inventory/received-items') ? 'active open': '' }} has-sub">
                        <a href="javascript:void(0)"><i
                                class="ik ik-bar-chart-2"></i><span>{{ __('Inventory / Stock') }}</span></a>
                        <div class="submenu-content">
                            @can('access_to_parts_receive')
                                <a href="{{ url('inventory') }}"
                                    class=" menu-item {{ \Request::is('inventory') || \Request::is('inventory/create') || \Request::is('inventory/show/*') || \Request::is('inventory/edit') ? 'active' : '' }}">{{ __('Parts Receive') }}</a>
                                <a href="{{ url('inventory/received-items') }}"
                                    class=" menu-item {{ \Request::is('inventory/received-items') ? 'active' : '' }}">{{ __('Received Item') }}</a>
                            @endcan

                            @can('access_to_central_wareHouse_parts_stock')
                                <a href="{{ url('inventory/stock') }}"
                                    class=" menu-item {{ \Request::is('inventory/stock') || \Request::is('inventory/show-inventory-details/*') ? 'active' : '' }}">{{ __('Central WareHouse Parts Stock') }}</a>
                            @endcan

                            @can('access_to_stock_in_hand')
                                <a href="{{ url('inventory/stock-in-hand') }}"
                                    class=" menu-item {{ \Request::is('inventory/stock-in-hand') || \Request::is('inventory/stock-in-hand-all') ? 'active' : '' }}">{{ __('Stock In Hand') }}</a>
                            @endcan

                            @canany(['access_to_inventory_branch_requisitions', 'access_to_branches_all_requisitions',
                                'access_to_branches_allocated_requisition', 'access_to_branches_re_allocated_requisition'])
                                <div
                                    class=" nav-item {{ \Request::is('central/requisitions/*') || \Request::is('central/requisitions') || \Request::is('central/re-allocations') || \Request::is('central/re-allocation/edit/*') || \Request::is('central/re-allocation/show/*') || \Request::is('central/requisitions/allocation/*') || \Request::is('central/requisitions/allocation') || \Request::is('central/requisition-item') || \Request::is('central/requisitions/allocation-item') || \Request::is('central/requisitions/re-allocation-item') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('central/requisitions/*') || \Request::is('central/requisitions') || \Request::is('central/re-allocations') || \Request::is('central/re-allocation/edit/*') || \Request::is('central/requisitions/allocation/*') || \Request::is('central/requisitions/allocation') || \Request::is('central/requisition-item') || \Request::is('central/requisitions/allocation-item') || \Request::is('central/requisitions/re-allocation-item') ? 'active open' : '' }} has-sub">{{ __('label.BRANCH_REQUISITIONS') }}</a>
                                    <div class="submenu-content">

                                        @can('access_to_branches_all_requisitions')
                                            <a href="{{ route('central.requisitions') }}"
                                                class="menu-item {{ \Request::is('central/requisitions') || \Request::is('central/requisitions/show/*') || \Request::is('central/requisitions/allocate/*') ? 'active' : '' }}">{{ __('label.BRANCH_REQUISITION_LIST') }}</a>
                                        @endcan
                                        @can('access_to_branches_all_requisitions')
                                            <a href="{{ route('central.requisition-item') }}"
                                                class="menu-item {{ \Request::is('central/requisition-item') ? 'active' : '' }}">{{ __('Requisition Item') }}</a>
                                        @endcan

                                        @can('access_to_branches_allocated_requisition')
                                            <a href="{{ route('central.allocation.index') }}"
                                                class="menu-item {{ \Request::is('central/requisitions/allocation/*') || \Request::is('central/requisitions/allocation') ? 'active' : '' }}">{{ __('label.BRANCH_ALLOCATED_LIST') }}</a>
                                            <a href="{{ route('central.requisitions.allocation-item') }}"
                                                class="menu-item {{ \Request::is('central/requisitions/allocation-item') ? 'active' : '' }}">{{ __('Allocated Item List') }}</a>
                                            <a href="{{ route('central.requisitions.re-allocation-item') }}"
                                                class="menu-item {{ \Request::is('central/requisitions/re-allocation-item') ? 'active' : '' }}">{{ __('Re-Allocated Item List') }}</a>
                                        @endcan

                                        @can('access_to_branches_re_allocated_requisition')
                                            <a href="{{ route('central.re-allocations') }}"
                                                class="menu-item {{ \Request::is('central/re-allocations') || \Request::is('central/re-allocation/show/*') || \Request::is('central/re-allocation/edit/*') ? 'active' : '' }}">{{ __('label.REALLOCATEION_REQUISITION_LIST') }}</a>
                                        @endcan

                                    </div>
                                </div>
                            @endcan

                            @canany(['access_to_central_branch_parts_return',
                                'access_to_central_branch_requested_parts_return',
                                'access_to_central_branch_received_parts_return'])
                                <div
                                    class=" nav-item {{ \Request::is('central/branch-parts-return/*') || \Request::is('central/branch-parts-return') || \Request::is('central/branch-parts-return/received/*') || \Request::is('central/branch-parts-return/received') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('central/branch-parts-return/*') || \Request::is('central/branch-parts-return') || \Request::is('central/branch-parts-return/received/*') || \Request::is('central/branch-parts-return/received') ? 'active open' : '' }} has-sub">{{ __('label.BRANCH_PARTS_RETURN') }}</a>
                                    <div class="submenu-content">
                                        @can('access_to_central_branch_requested_parts_return')
                                            <a href="{{ url('central/branch-parts-return') }}"
                                                class=" menu-item {{ \Request::is('central/branch-parts-return/show/*') || \Request::is('central/branch-parts-return') || \Request::is('central/branch-parts-return/show-details/*') || \Request::is('central/branch-parts-return/receive/*') ? 'active' : '' }}">{{ __('Requested Parts Return') }}</a>
                                        @endcan
                                        @can('access_to_central_branch_received_parts_return')
                                            <a href="{{ url('central/branch-parts-return/received') }}"
                                                class=" menu-item {{ \Request::is('central/branch-parts-return/received/*') || \Request::is('central/branch-parts-return/received') || \Request::is('central/branch-parts-return/received-details/*') || \Request::is('central/branch-parts-return/receive/edit/*') ? 'active' : '' }}">{{ __('Received Parts Return') }}</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan

                            @can('po_purchase_requistions')
                                <a href="{{ route('purchase.requisitions.index') }}"
                                    class=" menu-item {{ \Request::is('purchase/requisitions/*') || \Request::is('purchase/requisitions') ? 'active' : '' }}">{{ __('PO/Purchase Requistions') }}</a>
                            @endcan

                            <div
                                class=" nav-item {{ \Request::is('inventory/parts/*') || \Request::is('inventory/parts') || \Request::is('inventory/store') || \Request::is('inventory/part-category/*') || \Request::is('inventory/part-category') || \Request::is('inventory/price-management/*') || \Request::is('inventory/price-management') || \Request::is('inventory/parts_model/*') || \Request::is('inventory/parts_model') || \Request::is('inventory/store/*') || \Request::is('inventory/racks/*') || \Request::is('inventory/racks') || \Request::is('inventory/bins/*') || \Request::is('inventory/bins') || \Request::is('inventory/source/*') || \Request::is('inventory/source') || \Request::is('inventory/rack-bin-management/*') || \Request::is('inventory/rack-bin-management') ? 'active open' : '' }} has-sub">

                                @canany(['access_to_inventory_settings', 'parts_setting', 'parts_category_setting',
                                    'price_setting', 'parts_model_setting', 'source_setting', 'store_setting', 'rack_setting',
                                    'bin_setting'])
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('inventory/parts') || \Request::is('inventory/store') || \Request::is('inventory/part-category/*') || \Request::is('inventory/part-category') || \Request::is('inventory/price-management/*') || \Request::is('inventory/price-management') || \Request::is('inventory/parts_model/*') || \Request::is('inventory/parts_model') || \Request::is('inventory/store/*') || \Request::is('inventory/racks/*') || \Request::is('inventory/racks') || \Request::is('inventory/bins/*') || \Request::is('inventory/bins') || \Request::is('inventory/source/*') || \Request::is('inventory/source') || \Request::is('inventory/rack-bin-management/*') || \Request::is('inventory/rack-bin-management') ? 'active' : '' }} has-sub">Settings</a>
                                    <div class="submenu-content">

                                        @can('parts_category_setting')
                                            <a href="{{ url('inventory/part-category') }}"
                                                class="menu-item {{ \Request::is('inventory/part-category/*') || \Request::is('inventory/part-category') ? 'active' : '' }}">Parts
                                                Category</a>
                                        @endcan
                                        @can('parts_model_setting')
                                            <a href="{{ url('inventory/parts_model') }}"
                                                class="menu-item {{ \Request::is('inventory/parts_model/*') || \Request::is('inventory/parts_model') ? 'active' : '' }}">Parts
                                                Model</a>
                                        @endcan
                                        @can('parts_setting')
                                            <a href="{{ url('inventory/parts') }}"
                                                class="menu-item {{ \Request::is('inventory/parts/*') || \Request::is('inventory/parts') ? 'active' : '' }}">Parts</a>
                                        @endcan

                                        @can('price_setting')
                                            <a href="{{ url('inventory/price-management') }}"
                                                class="menu-item {{ \Request::is('inventory/price-management/*') || \Request::is('inventory/price-management') || \Request::is('inventory/price-management-history/*') ? 'active' : '' }}">Price</a>
                                        @endcan

                                        @can('source_setting')
                                            <a href="{{ url('inventory/source') }}"
                                                class="menu-item {{ \Request::is('inventory/source/*') || \Request::is('inventory/source') ? 'active' : '' }}">Source</a>
                                        @endcan

                                        @can('store_setting')
                                            <a href="{{ route('inventory.store.index') }}"
                                                class="menu-item {{ \Request::is('inventory/store') || \Request::is('inventory/store/*') ? 'active' : '' }}">{{ __('Store') }}</a>
                                        @endcan

                                        @can('rack_setting')
                                            <a href="{{ route('inventory.racks.index') }}"
                                                class="menu-item {{ \Request::is('inventory/racks/*') || \Request::is('inventory/racks') ? 'active' : '' }}">Rack</a>
                                        @endcan

                                        @can('bin_setting')
                                            <a href="{{ route('inventory.bins.index') }}"
                                                class="menu-item {{ \Request::is('inventory/bins/*') || \Request::is('inventory/bins') ? 'active' : '' }}">Bin</a>
                                        @endcan

                                        @can('bin_setting')
                                            <a href="{{ route('inventory.rack-bin-management.index') }}"
                                                class="menu-item {{ \Request::is('inventory/rack-bin-management/*') || \Request::is('inventory/rack-bin-management') ? 'active' : '' }}">Rack
                                                & Bin Management</a>
                                        @endcan
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endcan
                {{-- Cash Sell --}}
                @canany(['access_to_cash_sell', 'access_to_parts_sell'])
                    <div
                        class="nav-item {{ \Request::is('sell/parts-sell/*') || \Request::is('sell/parts-sell') || \Request::is('sell/parts-sell/create*') || \Request::is('sell/parts-sell/edit/*') ? 'active open' : '' }} has-sub">
                        <a href="javascript:void(0)"
                            class="menu-item {{ \Request::is('sell/parts-sell/*') || \Request::is('sell/parts-sell') || \Request::is('sell/parts-sell/create*') || \Request::is('sell/parts-sell/edit/*') ? 'active open' : '' }} has-sub"><i
                                class="ik ik-list"></i><span>{{ __('label.CASH_SALE') }}</span></a>
                        <div class="submenu-content">
                            @can('access_to_parts_sell')
                                <a href="{{ route('sell.direct-parts-sell-index') }}"
                                    class=" menu-item {{ \Request::is('sell/parts-sell') || \Request::is('sell/parts-sell/*') ? 'active' : '' }}">{{ __('label.PARTS_SALE') }}</a>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- Employee --}}
                @canany(['access_to_employee', 'access_to_team_leader', 'access_to_employee_attendance',
                    'designation_setting'])
                    <div
                        class=" nav-item {{ \Request::is('hrm/*') || \Request::is('hrm/') ? 'active open' : '' }} has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-users"></i><span>{{ __('Employee') }}</span></a>
                        <div class="submenu-content">

                            @can('access_to_employee')
                                <a href="{{ route('hrm.technician') }}"
                                    class=" menu-item {{ \Request::is('hrm/technician/*') || \Request::is('hrm/technician') ? 'active' : '' }}">{{ __('Employee') }}</a>
                            @endcan

                            @can('access_to_team_leader')
                                <a href="{{ route('hrm.teamleader.index') }}"
                                    class=" menu-item {{ \Request::is('hrm/teamleader/*') || \Request::is('hrm/teamleader') ? 'active' : '' }}">{{ __('Team Leader') }}</a>
                            @endcan

                            @can('access_to_employee_attendance')
                                <div
                                    class=" nav-item {{ \Request::is('hrm/attendance/*') || \Request::is('hrm/attendance') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class=" menu-item {{ \Request::is('hrm/attendance/*') || \Request::is('hrm/attendance') ? 'active open' : '' }} has-sub">Attendance</a>
                                    <div class="submenu-content">
                                        @can('access_to_employee_attendance')
                                            <a href="{{ url('hrm/attendance') }}"
                                                class=" menu-item {{ \Request::is('hrm/attendance') || \Request::is('hrm/attendance') ? 'active' : '' }}">Daily
                                                Attendance</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan

                            @can('designation_setting')
                                <div
                                    class=" nav-item {{ \Request::is('hrm/designation/*') || \Request::is('hrm/designation') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class=" menu-item {{ \Request::is('hrm/designation/*') || \Request::is('hrm/designation') ? 'active open' : '' }} has-sub">Settings</a>
                                    <div class="submenu-content">
                                        @can('designation_setting')
                                            <a href="{{ url('hrm/designation') }}"
                                                class=" menu-item {{ \Request::is('hrm/designation') || \Request::is('hrm/designation/*') ? 'active' : '' }}">Designation</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- Technician --}}
                @canany(['access_to_technician', 'access_to_technician_jobs', 'access_to_technician_jobs_list',
                    'access_to_submitted_jobs', 'access_to_advance_payment', 'access_to_technicians_inventory',
                    'access_to_parts_return', 'access_to_all_requisitions', 'access_to_allocated_requisitions',
                    'access_to_received_requisitions', 'access_to_technicians_stock', 'access_to_attendance'])
                    <div
                        class=" nav-item {{ \Request::is('technician/attendance') ||\Request::is('technician/jobs/*') ||\Request::is('technician/jobs') ||\Request::is('technician/consumption-by-job/*') ||\Request::is('customer-advanced-payment/*') ||\Request::is('customer-advanced-payment') ||\Request::is('technician/requisition') ||\Request::is('technician/requisition/*') ||\Request::is('job/employee/job-list') ||\Request::is('job/submitted-jobs/show/*') ||\Request::is('technician/submission/create/*') ||\Request::is('technician/submitted-jobs') ||\Request::is('technician/requisition-by-job/*') ||\Request::is('technician/requisition') ||\Request::is('inventory/technician/stock') ||\Request::is('technician/allocation') ||\Request::is('technician/allocation/show/*') ||\Request::is('technician/stock') ||\Request::is('technician/submitted-jobs/*') ||\Request::is('technician/parts-return') ||\Request::is('technician/parts-return/*') ||\Request::is('technician/requisition/allocate/receive/*') ||\Request::is('technician/requisition/allocate/receive') ||\Request::is('technician/allocations/receive/*') ||\Request::is('technician/allocations/receive') ||\Request::is('job/status/*') ||\Request::is('technician/withdraw-request/*') ||\Request::is('technician/withdraw-request')? 'active open': '' }} has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-user"></i><span>{{ __('Technician') }}</span></a>
                        <div class="submenu-content">
                            @canany(['access_to_technician_jobs', 'access_to_technician_jobs_list',
                                'access_to_submitted_jobs', 'access_to_advance_payment'])
                                <div
                                    class=" nav-item {{ \Request::is('technician/withdraw-request') || \Request::is('technician/withdraw-request/*') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class=" menu-item {{ \Request::is('technician/withdraw-request') || \Request::is('technician/withdraw-request/*') ? 'active open' : '' }} has-sub">Part
                                        Withdraw</a>
                                    <div class="submenu-content">

                                        @can('access_to_technician_jobs_list')
                                            <a href="{{ route('technician.withdraw-request.index') }}"
                                                class=" menu-item {{ \Request::is('technician/withdraw-request/*') || \Request::is('technician/withdraw-request') ? 'active' : '' }}">{{ __('Withdraw List') }}</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan
                            @canany(['access_to_technician_jobs', 'access_to_technician_jobs_list',
                                'access_to_submitted_jobs', 'access_to_advance_payment'])
                                <div
                                    class=" nav-item {{ \Request::is('technician/jobs') || \Request::is('technician/consumption-by-job/*') || \Request::is('customer-advanced-payment/*') || \Request::is('customer-advanced-payment') || \Request::is('job/submitted-jobs/show/*') || \Request::is('technician/submitted-jobs/*') || \Request::is('technician/submission/create/*') || \Request::is('technician/submitted-jobs') || \Request::is('job/status/*') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class=" menu-item {{ \Request::is('technician/jobs') || \Request::is('technician/jobs/*') || \Request::is('technician/consumption-by-job/*') || \Request::is('job/submitted-jobs/show/*') || \Request::is('technician/submission/create/*') || \Request::is('technician/submitted-jobs') || \Request::is('job/status/*') ? 'active open' : '' }} has-sub">Jobs</a>
                                    <div class="submenu-content">

                                        @can('access_to_technician_jobs_list')
                                            <a href="{{ route('technician.jobs') }}"
                                                class=" menu-item {{ \Request::is('technician/jobs/*') || \Request::is('technician/jobs') || \Request::is('job/status/*') ? 'active' : '' }}">{{ __('Job List') }}</a>
                                        @endcan

                                        @can('access_to_advance_payment')
                                            <a href="{{ url('customer-advanced-payment') }}"
                                                class=" menu-item {{ \Request::is('customer-advanced-payment/*') || \Request::is('customer-advanced-payment') ? 'active' : '' }}">{{ __('Advance Payment') }}</a>
                                        @endcan

                                        @can('access_to_submitted_jobs')
                                            <a href="{{ route('technician.submitted-jobs') }}"
                                                class=" menu-item {{ \Request::is('job/submitted-jobs/show/*') || \Request::is('technician/submitted-jobs') || \Request::is('technician/submitted-jobs/*') ? 'active' : '' }}">{{ __('Submitted Jobs') }}</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan
                            @canany(['access_to_technicians_inventory', 'access_to_parts_return',
                                'access_to_all_requisitions', 'access_to_allocated_requisitions',
                                'access_to_received_requisitions', 'access_to_technicians_stock'])
                                <div
                                    class=" nav-item {{ \Request::is('technician/requisition/*') || \Request::is('technician/requisition') || \Request::is('technician/requisition-by-job/*') || \Request::is('technician/allocation') || \Request::is('technician/stock') || \Request::is('technician/allocation') || \Request::is('technician/allocation/show/*') || \Request::is('technician/parts-return') || \Request::is('technician/parts-return/*') || \Request::is('technician/requisition/allocate/receive/*') || \Request::is('technician/requisition/allocate/receive') || \Request::is('technician/allocations/receive/*') || \Request::is('technician/allocations/receive') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class=" menu-item {{ \Request::is('technician/requisition/*') || \Request::is('technician/requisition') || \Request::is('technician/requisition-by-job/*') || \Request::is('technician/stock') || \Request::is('technician/allocation') || \Request::is('technician/parts-return') || \Request::is('technician/parts-return/*') || \Request::is('technician/requisition/allocate/receive/*') || \Request::is('technician/requisition/allocate/receive') || \Request::is('technician/allocations/receive/*') || \Request::is('technician/allocations/receive') || \Request::is('technician/stock_details/*') ? 'active open' : '' }} has-sub">Technician's
                                        Inventory</a>
                                    <div class="submenu-content">

                                        @can('access_to_parts_return')
                                            <a href="{{ route('technician.parts-return') }}"
                                                class=" menu-item {{ \Request::is('technician/parts-return/*') || \Request::is('technician/parts-return') ? 'active' : '' }}">{{ __('Parts Return') }}</a>
                                        @endcan

                                        @can('access_to_all_requisitions')
                                            <a href="{{ url('technician/requisition') }}"
                                                class=" menu-item {{ \Request::is('technician/requisition/*') || \Request::is('technician/requisition') ? 'active' : '' }}">{{ __('label.TECHNICIANS_REQUISITIONS') }}</a>
                                        @endcan

                                        @can('access_to_allocated_requisitions')
                                            <a href="{{ route('technician.allocation') }}"
                                                class=" menu-item {{ \Request::is('technician/allocation/*') || \Request::is('technician/allocation') ? 'active' : '' }}">{{ __('label.TECHNICIANS_REQUISITIONS_ALLOCATIONS') }}</a>
                                        @endcan

                                        @can('access_to_received_requisitions')
                                            {{-- <a href="{{route('technician.requisition.allocate.receive')}}" class=" menu-item {{ \Request::is('technician/requisition/allocate/receive/*') || \Request::is('technician/requisition/allocate/receive') ? 'active' : ''  }}">{{ __('label.RECEIVED_ALLOCATEION_REQUISITIONS')}}</a> --}}
                                            <a href="{{ route('technician.requisition.allocate.receive') }}"
                                                class=" menu-item {{ \Request::is('technician/allocations/receive/*') || \Request::is('technician/allocations/receive') ? 'active' : '' }}">{{ __('label.RECEIVED_ALLOCATEION_REQUISITIONS') }}</a>
                                        @endcan

                                        @can('access_to_technicians_stock')
                                            <a href="{{ route('technician.stock') }}"
                                                class=" menu-item {{ \Request::is('technician/stock') || \Request::is('technician/stock/*') || \Request::is('technician/stock_details/*') ? 'active' : '' }}">{{ __("Technician's Stock") }}</a>
                                        @endcan

                                    </div>
                                </div>
                            @endcan
                            @can('access_to_attendance')
                                <div
                                    class=" nav-item {{ \Request::is('technician/attendance/*') || \Request::is('technician/attendance') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class=" menu-item {{ \Request::is('technician/attendance/*') || \Request::is('technician/attendance') ? 'active open' : '' }} has-sub">Attendance</a>
                                    <div class="submenu-content">
                                        <a href="{{ route('technician.attendance') }}"
                                            class=" menu-item {{ \Request::is('technician/attendance') || \Request::is('technician/attendance/*') ? 'active' : '' }}">Daily
                                            Attendance</a>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- Tickets --}}
                @canany(['access_to_tickets', 'access_to_purchase', 'access_to_purchase_history',
                    'access_to_ticket_list', 'access_to_ticket_job_list', 'access_to_ticket_submited_job',
                    'access_to_ticket_settings', 'fault_setting', 'warranty_type_setting', 'service_type_setting',
                    'job_priority_setting', 'product_condition_setting', 'accessories_setting',
                    'product_receive_mode_setting', 'product_delivery_mode_setting'])
                    <div
                        class=" nav-item {{ \Request::is('tickets/*') || \Request::is('tickets') || \Request::is('product/purchase') || \Request::is('product/purchase/*') || \Request::is('job/job') || \Request::is('job/job/*') || \Request::is('job/submitted-jobs/*') || \Request::is('job/submitted-jobs') || \Request::is('inventory/fault/*') || \Request::is('inventory/fault') || \Request::is('tickets/warranty-types') || \Request::is('tickets/service-types') || \Request::is('tickets/job-priority') || \Request::is('tickets/product_conditions') || \Request::is('tickets/accessories') || \Request::is('tickets/receive-mode/*') || \Request::is('tickets/receive-mode') || \Request::is('tickets/delivery-mode/*') || \Request::is('tickets/delivery-mode') || \Request::is('job/status/*') ? 'active open' : '' }} has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-box"></i><span>Tickets</span></a>
                        <div class="submenu-content">

                            @canany('access_to_purchase')
                                <a href="{{ url('product/purchase') }}"
                                    class=" menu-item {{ \Request::is('tickets/purchase') || \Request::is('product/purchase') || \Request::is('product/purchase/*') ? 'active' : '' }}">Purchase</a>
                            @endcan

                            @can('access_to_purchase_history')
                                <a href="{{ route('customer-purchase-history') }}"
                                    class=" menu-item {{ \Request::is('tickets/customer-purchase-history') || \Request::is('tickets/ticket-purchase-show/*') ? 'active' : '' }}">Purchase
                                    History</a>
                            @endcan
                            @can('access_to_ticket_list')
                                <a href="{{ url('tickets/ticket-index') }}"
                                    class=" menu-item {{ \Request::is('tickets/ticket/show/*') || \Request::is('tickets/ticket-index') || \Request::is('tickets/status/*') || \Request::is('tickets/ticket-create/*') || \Request::is('tickets/ticket/edit/*') ? 'active' : '' }}">Ticket
                                    List</a>
                            @endcan

                            @can('access_to_ticket_job_list')
                                <a href="{{ url('job/job') }}"
                                    class=" menu-item {{ \Request::is('job/job/show/*') || \Request::is('job/job') || \Request::is('job/job/*') || \Request::is('job/status/*') ? 'active' : '' }}">Job
                                    List</a>
                            @endcan

                            @can('access_to_ticket_submited_job')
                                <a href="{{ route('job.submitted-jobs.index') }}"
                                    class=" menu-item {{ \Request::is('job/submitted-jobs/show/*') || \Request::is('job/submitted-jobs') ? 'active' : '' }}">{{ __('Submitted Jobs') }}</a>
                            @endcan

                            @canany(['access_to_ticket_settings', 'fault_setting', 'warranty_type_setting',
                                'service_type_setting', 'job_priority_setting', 'product_condition_setting',
                                'accessories_setting', 'product_receive_mode_setting', 'product_delivery_mode_setting'])
                                <div
                                    class=" nav-item {{ \Request::is('inventory/fault/*') || \Request::is('inventory/fault') || \Request::is('tickets/warranty-types/*') || \Request::is('tickets/warranty-types') || \Request::is('tickets/service-types/*') || \Request::is('tickets/service-types') || \Request::is('tickets/job-priority') || \Request::is('tickets/product_conditions') || \Request::is('tickets/accessories') || \Request::is('tickets/receive-mode/*') || \Request::is('tickets/receive-mode') || \Request::is('tickets/delivery-mode/*') || \Request::is('tickets/delivery-mode') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class=" menu-item {{ \Request::is('inventory/fault/*') || \Request::is('inventory/fault') || \Request::is('tickets/warranty-types/*') || \Request::is('tickets/warranty-types') || \Request::is('tickets/service-types/*') || \Request::is('tickets/service-types') || \Request::is('tickets/service-types/*') || \Request::is('tickets/job-priority') || \Request::is('tickets/product_conditions') || \Request::is('tickets/accessories') || \Request::is('tickets/receive-mode/*') || \Request::is('tickets/receive-mode') || \Request::is('tickets/delivery-mode/*') || \Request::is('tickets/delivery-mode') ? 'active open' : '' }} has-sub">Settings</a>

                                    <div class="submenu-content">
                                        @can('fault_setting')
                                            <a href="{{ url('inventory/fault') }}"
                                                class="menu-item {{ \Request::is('inventory/fault/*') || \Request::is('inventory/fault') ? 'active' : '' }}">Fault</a>
                                        @endcan

                                        @can('warranty_type_setting')
                                            <a href="{{ url('tickets/warranty-types') }}"
                                                class="menu-item {{ \Request::is('tickets/warranty-types') || \Request::is('tickets/warranty-types/*') ? 'active' : '' }}">Warranty
                                                Type</a>
                                        @endcan

                                        @can('service_type_setting')
                                            <a href="{{ url('tickets/service-types') }}"
                                                class="menu-item {{ \Request::is('tickets/service-types') || \Request::is('tickets/service-types/*') ? 'active' : '' }}">Service
                                                Type</a>
                                        @endcan

                                        @can('job_priority_setting')
                                            <a href="{{ url('tickets/job-priority') }}"
                                                class="menu-item {{ \Request::is('tickets/job-priority') ? 'active' : '' }}">Job
                                                Priority</a>
                                        @endcan

                                        @can('product_condition_setting')
                                            <a href="{{ url('tickets/product_conditions') }}"
                                                class="menu-item {{ \Request::is('tickets/product_conditions') ? 'active' : '' }}">Product
                                                Condition</a>
                                        @endcan

                                        @can('accessories_setting')
                                            <a href="{{ url('tickets/accessories') }}"
                                                class="menu-item {{ \Request::is('tickets/accessories') ? 'active' : '' }}">Accessories</a>
                                        @endcan

                                        @can('product_receive_mode_setting')
                                            <a href="{{ url('tickets/receive-mode') }}"
                                                class="menu-item {{ \Request::is('tickets/receive-mode/*') || \Request::is('tickets/receive-mode') ? 'active' : '' }}">Product
                                                Receive Mode</a>
                                        @endcan

                                        @can('product_delivery_mode_setting')
                                            <a href="{{ url('tickets/delivery-mode') }}"
                                                class="menu-item {{ \Request::is('tickets/delivery-mode/*') || \Request::is('tickets/delivery-mode') ? 'active' : '' }}">Product
                                                Delivery Mode</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- Branch  --}}
                @canany(['access_to_branch', 'access_to_branch_requisitions', 'access_to_branch_all_requisitions',
                    'access_to_branch_allocated_requisitions', 'access_to_branch_re_allocated_requisitions',
                    'access_to_branch_received_requisitions', 'access_to_all_technician_requisitions',
                    'access_to_all_technician_all_requisitions', 'access_to_all_technician_allocated_requisitions',
                    'access_to_branch_parts_return', 'access_to_technicians_parts_return',
                    'access_to_requested_parts_return', 'access_to_received_parts_return',
                    'access_to_branch_parts_transfer', 'access_to_branch_outgoing_parts_transfer',
                    'access_to_branch_outgoing_parts_transfer_list',
                    'access_to_branch_outgoing_parts_transfer_allocation_list',
                    'access_to_branch_outgoing_parts_transfer_received_list', 'access_to_branch_incoming_parts_transfer',
                    'access_to_branch_incoming_parts_transfer_list',
                    'access_to_branch_incoming_parts_transfer_allocation_list', 'access_to_branch_inventory'])
                    <div
                        class=" nav-item {{ \Request::is('branch/requisitions/*') ||\Request::is('branch/requisitions') ||\Request::is('branch/technician-requisitions/show/*') ||\Request::is('branch/allocations') ||\Request::is('branch/technician-requisitions') ||\Request::is('technician/requisition/allocate/*') ||\Request::is('inventory/stock/outlet') ||\Request::is('loan/loan-request') ||\Request::is('loan/accept-loan') ||\Request::is('allocation/branch-allocation') ||\Request::is('branch/re-allocations') ||\Request::is('branch/stocks') ||\Request::is('branch/stock/details/*') ||\Request::is('branch/parts-return') ||\Request::is('branch/receive/*') ||\Request::is('branch/receive') ||\Request::is('branch/technician/requisitions/*') ||\Request::is('branch/requisition-allocate/*') ||\Request::is('branch/technician/requisitions') ||\Request::is('branch/technician/allocations/*') ||\Request::is('branch/technician/allocations') ||\Request::is('branch/branch-parts-return') ||\Request::is('branch/branch-parts-return/*') ||\Request::is('branch/parts-return/received') ||\Request::is('branch/parts-return/received/*') ||\Request::is('loan/loan-request/*') ||\Request::is('loan/loan-request') ||\Request::is('loan/allocated-list/*') ||\Request::is('loan/allocated-list') ||\Request::is('loan/all-received-loans') ||\Request::is('loan/all-received-loans/*') ||\Request::is('loan/all-accepted-loans') ||\Request::is('loan/all-accepted-loans/*') ||\Request::is('branch/parts-return/receive/*') ||\Request::is('branch/allocation/show/*')? 'active open': '' }}  has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-box"></i><span>{{ __('label.OUTLET') }}</span></a>
                        <div class="submenu-content">

                            @canany(['access_to_branch_requisitions', 'access_to_branch_all_requisitions',
                                'access_to_branch_allocated_requisitions', 'access_to_branch_re_allocated_requisitions',
                                'access_to_branch_received_requisitions'])
                                <div
                                    class=" nav-item {{ \Request::is('branch/requisitions/*') || \Request::is('branch/requisitions') || \Request::is('branch/allocations') || \Request::is('branch/re-allocations') || \Request::is('branch/receive/*') || \Request::is('branch/receive') || \Request::is('branch/allocation/show/*') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('branch/requisitions/*') || \Request::is('branch/requisitions') || \Request::is('branch/allocations') || \Request::is('branch/re-allocations') || \Request::is('branch/receive/*') || \Request::is('branch/receive') || \Request::is('branch/allocation/show/*') ? 'active open' : '' }} has-sub">{{ __('label.BRANCH_REQUISITIONS') }}</a>
                                    <div class="submenu-content">

                                        @can('access_to_branch_all_requisitions')
                                            <a href="{{ route('branch.requisitions') }}"
                                                class="menu-item {{ \Request::is('branch/requisitions') || \Request::is('branch/requisitions/*') ? 'active' : '' }}">{{ __('label.BRANCH_REQUISITION_LIST') }}</a>
                                        @endcan

                                        @can('access_to_branch_allocated_requisitions')
                                            <a href="{{ route('branch.allocations') }}"
                                                class="menu-item {{ \Request::is('branch/allocations') || \Request::is('branch/allocation/show/*') ? 'active' : '' }}">{{ __('label.BRANCH_ALLOCATED_LIST') }}</a>
                                        @endcan

                                        @can('access_to_branch_re_allocated_requisitions')
                                            <a href="{{ route('branch.re-allocations') }}"
                                                class="menu-item {{ \Request::is('branch/re-allocations') ? 'active' : '' }}">{{ __('label.REALLOCATEION_REQUISITION_LIST') }}</a>
                                        @endcan

                                        @can('access_to_branch_received_requisitions')
                                            <a href="{{ route('branch.allocation.received.index') }}"
                                                class="menu-item {{ \Request::is('branch/receive/*') || \Request::is('branch/receive') ? 'active' : '' }}">{{ __('label.RECEIVED_ALLOCATEION_REQUISITIONS') }}</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan

                            @canany(['access_to_all_technician_requisitions', 'access_to_all_technician_all_requisitions',
                                'access_to_all_technician_allocated_requisitions'])
                                <div
                                    class=" nav-item {{ \Request::is('branch/technician/requisitions/*') || \Request::is('branch/technician/requisitions') || \Request::is('branch/technician/allocations/*') || \Request::is('branch/technician/allocations') || \Request::is('branch/technician-requisitions/show/*') || \Request::is('branch/requisition-allocate/*') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('branch/technician/requisitions/*') || \Request::is('branch/technician/requisitions') || \Request::is('branch/technician/allocations/*') || \Request::is('branch/technician/allocations') || \Request::is('branch/technician-requisitions/show/*') || \Request::is('branch/requisition-allocate/*') ? 'active open' : '' }} has-sub">{{ __('label.MENU_TECHNICIANS_REQUISITIONS') }}</a>

                                    <div class="submenu-content">

                                        @can('access_to_all_technician_all_requisitions')
                                            <a href="{{ route('branch.technician-requisitions') }}"
                                                class="menu-item {{ \Request::is('branch/technician/requisitions/*') || \Request::is('branch/technician/requisitions') ? 'active' : '' }}">{{ __('label.TECHNICIANS_REQUISITIONS') }}</a>
                                        @endcan

                                        @can('access_to_all_technician_allocated_requisitions')
                                            <a href="{{ route('branch.technician.allocations') }}"
                                                class="menu-item {{ \Request::is('branch/technician/allocations/*') || \Request::is('branch/technician/allocations') ? 'active' : '' }}">{{ __('label.TECHNICIANS_REQUISITIONS_ALLOCATIONS') }}</a>
                                        @endcan
                                    </div>

                                </div>
                            @endcan

                            @can('access_to_branch_parts_return')
                                <a href="{{ url('branch/branch-parts-return') }}"
                                    class="menu-item {{ \Request::is('branch/branch-parts-return') || \Request::is('branch/branch-parts-return/*') ? 'active' : '' }}">{{ __('Branch Parts Return') }}</a>
                            @endcan

                            @canany(['access_to_technicians_parts_return', 'access_to_requested_parts_return',
                                'access_to_received_parts_return'])
                                <div
                                    class=" nav-item {{ \Request::is('branch/parts-return') || \Request::is('branch/parts-return/received') || \Request::is('branch/parts-return/received/*') || \Request::is('branch/parts-return/receive/*') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('branch/parts-return') || \Request::is('branch/parts-return/received') || \Request::is('branch/parts-return/received/*') || \Request::is('branch/parts-return/receive/*') ? 'active open' : '' }} has-sub">{{ __('Technicians Parts Returns') }}</a>

                                    <div class="submenu-content">

                                        @can('access_to_requested_parts_return')
                                            <a href="{{ url('branch/parts-return') }}"
                                                class="menu-item {{ \Request::is('branch/parts-return') || \Request::is('branch/parts-return/show/*') ? 'active' : '' }}">{{ __('Requested Parts Return') }}</a>
                                        @endcan

                                        @can('access_to_received_parts_return')
                                            <a href="{{ route('branch.parts-return.received') }}"
                                                class="menu-item {{ \Request::is('branch/parts-return/received') || \Request::is('branch/parts-return/received/*') ? 'active' : '' }}">{{ __('Received Parts Return') }}</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan

                            @canany(['access_to_branch_parts_transfer', 'access_to_branch_outgoing_parts_transfer',
                                'access_to_branch_outgoing_parts_transfer_list',
                                'access_to_branch_outgoing_parts_transfer_allocation_list',
                                'access_to_branch_outgoing_parts_transfer_received_list',
                                'access_to_branch_incoming_parts_transfer', 'access_to_branch_incoming_parts_transfer_list',
                                'access_to_branch_incoming_parts_transfer_allocation_list'])
                                <div
                                    class=" nav-item {{ \Request::is('loan/loan-request/*') || \Request::is('loan/loan-request') || \Request::is('loan/allocated-list/*') || \Request::is('loan/allocated-list') || \Request::is('loan/all-received-loans') || \Request::is('loan/all-received-loans/*') || \Request::is('loan/accept-loan/*') || \Request::is('loan/accept-loan') || \Request::is('loan/all-accepted-loans') || \Request::is('loan/all-accepted-loans/*') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('loan/loan-request/*') || \Request::is('loan/loan-request') || \Request::is('loan/allocated-list/*') || \Request::is('loan/allocated-list') || \Request::is('loan/all-received-loans') || \Request::is('loan/all-received-loans/*') || \Request::is('loan/accept-loan/*') || \Request::is('loan/accept-loan') || \Request::is('loan/all-accepted-loans') || \Request::is('loan/all-accepted-loans/*') ? 'active open' : '' }} has-sub">{{ __('Parts Transfer') }}</a>

                                    <div class="submenu-content">

                                        @canany(['access_to_branch_outgoing_parts_transfer',
                                            'access_to_branch_outgoing_parts_transfer_list',
                                            'access_to_branch_outgoing_parts_transfer_allocation_list',
                                            'access_to_branch_outgoing_parts_transfer_received_list'])
                                            <div
                                                class=" nav-item {{ \Request::is('loan/loan-request/*') || \Request::is('loan/loan-request') || \Request::is('loan/allocated-list/*') || \Request::is('loan/allocated-list') || \Request::is('loan/all-received-loans/*') || \Request::is('loan/all-received-loans') ? 'active open' : '' }} has-sub">

                                                <a href="javascript:void(0)"
                                                    class="menu-item {{ \Request::is('loan/loan-request/*') || \Request::is('loan/loan-request') || \Request::is('loan/allocated-list/*') || \Request::is('loan/allocated-list') || \Request::is('loan/all-received-loans') || \Request::is('loan/all-received-loans/*') ? 'active open' : '' }} has-sub">{{ __('Outgoing Parts Transfer') }}</a>

                                                <div class="submenu-content">

                                                    @can('access_to_branch_outgoing_parts_transfer_list')
                                                        <a href="{{ url('loan/loan-request') }}"
                                                            class="menu-item {{ \Request::is('loan/loan-request/*') || \Request::is('loan/loan-request') ? 'active' : '' }}">{{ __('Transfer List') }}</a>
                                                    @endcan

                                                    @can('access_to_branch_outgoing_parts_transfer_allocation_list')
                                                        <a href="{{ route('loan.loan-allocated.list') }}"
                                                            class="menu-item {{ \Request::is('loan/allocated-list/*') || \Request::is('loan/allocated-list') ? 'active' : '' }}">{{ __('Allocation List') }}</a>
                                                    @endcan

                                                    @can('access_to_branch_outgoing_parts_transfer_received_list')
                                                        <a href="{{ route('loan.received-loans') }}"
                                                            class="menu-item {{ \Request::is('loan/all-received-loans') || \Request::is('loan/all-received-loans/*') ? 'active' : '' }}">{{ __('Received List') }}</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        @endcan

                                        @canany(['access_to_branch_incoming_parts_transfer',
                                            'access_to_branch_incoming_parts_transfer_list',
                                            'access_to_branch_incoming_parts_transfer_allocation_list'])
                                            <div
                                                class=" nav-item {{ \Request::is('loan/accept-loan/*') || \Request::is('loan/accept-loan') || \Request::is('loan/all-accepted-loans') || \Request::is('loan/all-accepted-loans/*') ? 'active open' : '' }} has-sub">
                                                <a href="javascript:void(0)"
                                                    class="menu-item {{ \Request::is('loan/accept-loan/*') || \Request::is('loan/accept-loan') || \Request::is('loan/all-accepted-loans') || \Request::is('loan/all-accepted-loans/*') ? 'active open' : '' }} has-sub">{{ __('Incoming Parts Transfer') }}</a>

                                                <div class="submenu-content">
                                                    @can('access_to_branch_incoming_parts_transfer_list')
                                                        <a href="{{ url('loan/accept-loan') }}"
                                                            class="menu-item {{ \Request::is('loan/accept-loan/*') || \Request::is('loan/accept-loan') ? 'active' : '' }}">{{ __('Transfer List') }}</a>
                                                    @endcan

                                                    @can('access_to_branch_incoming_parts_transfer_allocation_list')
                                                        <a href="{{ route('loan.accepted-loans') }}"
                                                            class="menu-item {{ \Request::is('loan/all-accepted-loans') || \Request::is('loan/all-accepted-loans/*') ? 'active' : '' }}">{{ __('Allocation List') }}</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            @endcan

                            @can('access_to_branch_inventory')
                                <a href="{{ route('branch.stocks') }}"
                                    class="menu-item {{ \Request::is('branch/stocks') ? 'active' : '' }}">{{ __('label.BRANCH_INVENTORY') }}</a>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- Customer --}}
                @canany(['access_to_customers', 'access_to_customer_settings', 'customer_grade_setting',
                    'feedback_question_setting'])
                    <div
                        class="nav-item {{ \Request::is('call-center/customer/*') || \Request::is('call-center/customer') || \Request::is('call-center/customer-grade/*') || \Request::is('call-center/customer-grade') || \Request::is('call-center/customer-feedback-question/*') || \Request::is('call-center/customer-feedback-question') || \Request::is('call-center/customer-feedback-question/create') ? 'active open' : '' }} has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-list"></i><span>Customer</span></a>
                        <div class="submenu-content">

                            @can('access_to_customers')
                                <a href="{{ route('call-center.customer-index') }}"
                                    class="menu-item {{ \Request::is('call-center/customer') || \Request::is('call-center/customer/*') ? 'active' : '' }}">Customers</a>
                            @endcan

                            @canany(['access_to_customer_settings', 'customer_grade_setting', 'feedback_question_setting'])
                                <div
                                    class="nav-item {{ \Request::is('call-center/customer-grade') || \Request::is('call-center/customer-feedback-question') ? 'active open' : '' }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item {{ \Request::is('call-center/customer-grade') || \Request::is('call-center/customer-feedback-question') || \Request::is('call-center/customer-grade/*') || \Request::is('call-center/customer-feedback-question/*') ? 'active open' : '' }}">Settings</a>
                                    <div class="submenu-content">
                                        @can('customer_grade_setting')
                                            <a href="{{ url('call-center/customer-grade') }}"
                                                class="menu-item {{ \Request::is('call-center/customer-grade') || \Request::is('call-center/customer-grade/*') ? 'active open' : '' }}">Customer
                                                Grade</a>
                                        @endcan

                                        @can('feedback_question_setting')
                                            <a href="{{ url('call-center/customer-feedback-question') }}"
                                                class="menu-item {{ \Request::is('call-center/customer-feedback-question') || \Request::is('call-center/customer-feedback-question/*') ? 'active' : '' }}">Feedback
                                                Question</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan
                @canany(['access_to_accounts', 'access_to_account_list', 'access_to_cash_transections',
                    'access_to_deposit', 'access_to_expense', 'access_to_revenue', 'access_to_cash_ledger',
                    'access_to_account_settings', 'expense_item_setting'])
                    <div
                        class="nav-item {{ \Request::is('bank-account') || \Request::is('bank-account/*') || \Request::is('deposit') || \Request::is('deposit/*') || \Request::is('expense') || \Request::is('expense/*') || \Request::is('revenue') || \Request::is('revenue/*') || \Request::is('cash-transections') || \Request::is('cash-transections/*') || \Request::is('pettycash') || \Request::is('expense-items/*') || \Request::is('expense-items') || \Request::is('transections/branch/show/*') ? 'active open' : '' }} has-sub">
                        <a href="javascript:void(0)"><i class="ik ik-award"></i><span>{{ __('Accounts') }}</span></a>
                        <div class="submenu-content">
                            @can('access_to_account_list')
                                <a href="{{ route('bank-account-index') }}"
                                    class="menu-item {{ \Request::is('bank-account') || \Request::is('bank-account/*') ? 'active' : '' }}">{{ __('Account List') }}</a>
                            @endcan

                            @can('access_to_cash_transections')
                                <a href="{{ route('cash-transections.index') }}"
                                    class="menu-item {{ \Request::is('cash-transections') || \Request::is('cash-transections/*') ? 'active' : '' }}">{{ __('Cash Transactions') }}</a>
                            @endcan

                            @can('access_to_deposit')
                                <a href="{{ route('deposit-index') }}"
                                    class="menu-item {{ \Request::is('deposit') || \Request::is('deposit/*') ? 'active' : '' }}">{{ __('Deposit') }}</a>
                            @endcan

                            @can('access_to_expense')
                                <a href="{{ route('expense-index') }}"
                                    class="menu-item {{ \Request::is('expense') || \Request::is('expense/*') ? 'active' : '' }}">{{ __('Expense') }}</a>
                            @endcan

                            @can('access_to_revenue')
                                <a href="{{ route('revenue-index') }}"
                                    class="menu-item {{ \Request::is('revenue') || \Request::is('revenue/*') ? 'active' : '' }}">{{ __('Revenue') }}</a>
                            @endcan

                            @can('access_to_cash_ledger')
                                <a href="{{ route('transections.pettycash') }}"
                                    class="menu-item {{ \Request::is('pettycash') || \Request::is('pettycash/*') || \Request::is('transections/branch/show/*') ? 'active' : '' }}">{{ __('Cash Ledger') }}</a>
                            @endcan

                            @canany(['access_to_account_settings', 'expense_item_setting'])
                                <div
                                    class="nav-item{{ \Request::is('expense-items') || \Request::is('expense-items/*') }} has-sub">
                                    <a href="javascript:void(0)"
                                        class="menu-item{{ \Request::is('expense-items') || \Request::is('expense-items/*') ? 'active open' : '' }}">Settings</a>
                                    <div class="submenu-content">
                                        @can('expense_item_setting')
                                            <a href="{{ route('expense-items.index') }}"
                                                class="menu-item {{ \Request::is('expense-items') || \Request::is('expense-items/*') ? 'active' : '' }}">Expense
                                                Items</a>
                                        @endcan
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                @canany(['access_to_adminstrator', 'access_to_users', 'access_to_add_user', 'access_to_roles',
                    'access_to_permission'])
                    <div
                        class="nav-item {{ $segment1 == 'users' || $segment1 == 'roles' || $segment1 == 'permission' || $segment1 == 'user' ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-user"></i><span>{{ __('Administrator') }}</span></a>
                        <div class="submenu-content">
                            <!-- only those have manage_user permission will get access -->
                            @canany(['access_to_users', 'access_to_add_user'])
                                <a href="{{ url('users') }}"
                                    class="menu-item {{ $segment1 == 'users' ? 'active' : '' }}">{{ __('Users') }}</a>
                                <a href="{{ url('user/create') }}"
                                    class="menu-item {{ $segment1 == 'user' && $segment2 == 'create' ? 'active' : '' }}">{{ __('Add User') }}</a>
                            @endcan

                            <!-- only those have manage_role permission will get access -->
                            @can('access_to_roles')
                                <a href="{{ url('roles') }}"
                                    class="menu-item {{ $segment1 == 'roles' ? 'active' : '' }}">{{ __('Roles') }}</a>
                            @endcan

                            <!-- only those have manage_permission permission will get access -->
                            {{-- @can('access_to_permission')
                            <a href="{{url('permission')}}" class="menu-item {{ ($segment1 == 'permission') ? 'active' : '' }}">{{ __('Permission')}}</a>
                            @endcan --}}
                        </div>
                    </div>
                @endcan
                @canany(['access_to_adminstrator', 'access_to_users', 'access_to_add_user', 'access_to_roles',
                    'access_to_permission'])
                    <div
                        class="nav-item {{ \Request::is('report/job-report-get') || \Request::is('report/job-report-post') || \Request::is('report/kpi-report-get') || \Request::is('report/kpi-report-post') || \Request::is('report/consumption-report-get') || \Request::is('report/consumption-report-post') || \Request::is('report/finance-report-get') || \Request::is('report/finance-report-get') ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-file-text"></i><span>{{ __('Reports') }}</span></a>
                        <div class="submenu-content">
                            <!-- only those have manage_user permission will get access -->
                            @canany(['access_to_users', 'access_to_add_user'])
                                <a href="{{ route('report.job-report-get') }}"
                                    class="menu-item {{ \Request::is('report/job-report-get') || \Request::is('report/job-report-post') ? 'active' : '' }}">{{ __('Job Report') }}</a>
                            @endcan
                            @canany(['access_to_users', 'access_to_add_user'])
                                <a href="{{ route('report.kpi-report-get') }}"
                                    class="menu-item {{ \Request::is('report/kpi-report-get') || \Request::is('report/kpi-report-post') ? 'active' : '' }}">{{ __('Kpi Report') }}</a>
                            @endcan
                            @canany(['access_to_users', 'access_to_add_user'])
                                <a href="{{ route('report.consumption-report-get') }}"
                                    class="menu-item {{ \Request::is('report/consumption-report-get') || \Request::is('report/consumption-report-post') ? 'active' : '' }}">{{ __('Consumption Report') }}</a>
                            @endcan
                            @canany(['access_to_users', 'access_to_add_user'])
                                <a href="{{ route('report.finance-report-get') }}"
                                    class="menu-item {{ \Request::is('report/finance-report-get') || \Request::is('report/finance-report-get') ? 'active' : '' }}">{{ __('Financial Report') }}</a>
                            @endcan
                        </div>
                    </div>
                @endcan
        </div>
    </div>
</div>
