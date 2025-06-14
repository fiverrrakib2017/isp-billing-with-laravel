@extends('Backend.Layout.App')
@section('title', 'Dashboard | Customer Onu List | Admin Panel')
@section('style')

{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <div class="card-header">
                <marquee behavior="scroll" direction="left" scrollamount="5" style="color: #0c5460; font-weight: bold; background-color: #d1ecf1; padding: 8px; border-radius: 5px;">
                    📢 নোটিশ: এই তালিকায় থাকা কাস্টমাররা তাদের ইন্টারনেট সংযোগ বাতিল করেছেন। অনু/ONU ডিভাইস যদি কোম্পানি প্রদান করে থাকে, তবে তা দ্রুত সংগ্রহ করুন এবং স্টকে আপডেট করুন। সংযোগ বাতিলের তারিখ ও অনু টাইপ যাচাই করে প্রয়োজনীয় পদক্ষেপ গ্রহণ করুন। ✅
                </marquee>

            </div>
            <div class="card-body">
                <div class="table-responsive" id="tableStyle">
                    <div class="col-6 nav justify-content-end" id="export_buttonscc"></div>
                        <table id="customer_datatable1" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Package</th>
                                    <th>Amount</th>


                                    <th>Mobile No.</th>
                                    <th>POP/Branch</th>
                                    <th>Area/Location</th>
                                    <th>Onu Type</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                </div>
            </div>
        </div>

    </div>
</div>


@php
   //$branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
    if(!empty($branch_user_id) && $branch_user_id > 0){
        $pop_branches = \App\Models\Pop_branch::where('id',$branch_user_id)->first();
        $areas=\App\Models\Pop_area::where('status','active')->where('pop_id',$branch_user_id)->get();
    }else{
        $pop_branches=\App\Models\Pop_branch::where('status',1)->get();
        $areas=\App\Models\Pop_area::where('status','active')->get();
    }

    /*GET Request POP/Branch View Table*/
    if(!empty($pop_id) && $pop_id > 0 && isset($pop_id)){
        $pop_branches = \App\Models\Pop_branch::where('id',$pop_id)->get();
        $areas = \App\Models\Pop_area::where('status','active')->where('pop_id',$pop_id)->get();
    }else{
        $pop_branches = \App\Models\Pop_branch::where('status',1)->get();
        $areas = \App\Models\Pop_area::where('status','active')->get();
    }
@endphp

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        /*Get Url Param Recevied*/
        var pop_id = @json($pop_id ?? '');
        var area_id = @json($area_id ?? '');
        var status = @json($status ?? '');

        /*When Request Get Area Page*/
       var  area_page = @json($area_page ?? false);

        /* GET POP-Branch */
        var pop_branches = @json($pop_branches);
        var pop_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select id="search_pop_id" name="search_pop_id" class="form-control form-control-sm select2">
                    <option value="">--Select POP/Branch--</option>`;
        pop_branches.forEach(function(item) {
            pop_filter += `<option value="${item.id}">${item.name}</option>`;
        });
        pop_filter += `</select></div>`;

        /* Get Areas */
        var areas = @json($areas);
        var area_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select id="search_area_id" name="search_area_id" class="form-control form-control-sm select2">
                    <option value="">--Select Area--</option>`;
        areas.forEach(function(item) {
            area_filter += `<option value="${item.id}">${item.name}</option>`;
        });
        area_filter += `</select></div>`;

        /* Status Filter */
        var onu_type_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select class="onu_type_filter form-control form-control-sm select2">
                    <option value="">--Onu Type--</option>
                    <option value="customer">Customer</option>
                    <option value="company">Company</option>
                </select>
            </div>`;

        setTimeout(() => {
            var filters_wrapper = `
                <div class="row no-gutters mb-0  " style=" row-gap: 0.5rem;">
                    <!-- Left: Per Page -->
                    <div class="col-12 col-md-auto dataTables_length_container d-flex align-items-center mb-2 mb-md-0 pr-md-3"></div>

                    <!-- Middle: Filters -->
                    <div class="col-12 col-md d-flex flex-wrap align-items-center mb-2 mb-md-0" style="gap: 0.5rem;">
                        ${pop_filter + area_filter + onu_type_filter}
                    </div>

                    <!-- Right: Search Input -->
                    <div class="col-12 col-md-auto dataTables_filter_container d-flex justify-content-md-end"></div>
                </div>
            `;
            /* Append the filters to the DataTable wrapper */
            if(area_page==false){
                var tableWrapper = $('#customer_datatable1').closest('.dataTables_wrapper');
                tableWrapper.prepend(filters_wrapper);

                tableWrapper.find('.dataTables_length').appendTo(tableWrapper.find('.dataTables_length_container'));
                tableWrapper.find('.dataTables_filter').appendTo(tableWrapper.find('.dataTables_filter_container'));
            }



            $('#search_pop_id').select2({ width: 'resolve' });
            $('#search_area_id').select2({ width: 'resolve' });
            $('.onu_type_filter').select2({ width: 'resolve' });
        }, 1000);




        /*Check Param Values if else */
        if (!pop_id) {
            pop_id = $('#search_pop_id').val();
        }
        if (!area_id) {
            area_id = $('#search_area_id').val();
        }


        $(document).on('change','select[name="search_pop_id"]',function(){
            var areas = @json($areas);
            var selectedPopId = $(this).val();
            var filteredAreas = areas.filter(function(item) {
                return item.pop_id == selectedPopId;
            });
            var areasOptions = '<option value="">--Select Area--</option>';
            filteredAreas.forEach(function(item) {
                areasOptions += '<option value="' + item.id + '">' + item.name + '</option>';
            });
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
            $('select[name="search_area_id"]').html(areasOptions);
        });
        /*Handle Area filter change*/
        $(document).on('change', 'select[name="search_area_id"]', function() {
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
        });
        /*Handle Status filter change*/
        $(document).on('change', '.onu_type_filter', function() {
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
        });

        var customer_table = $("#customer_datatable1").DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            beforeSend: function() {

            },
            complete: function() {

            },
            "ajax": {
                url: "{{ route('admin.customer.get_customer_onu_list_data') }}",
                type: "GET",
                data: function(d) {
                    d.pop_id        = $('#search_pop_id').val() || pop_id;
                    d.area_id       = $('#search_area_id').val() || area_id;
                    d.onu_type      = $('.onu_type_filter').val() ;
                }
            },
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_ items/page',
            },
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "username",
                    "render": function(data, type, row) {
                        var viewUrl = '{{ route('admin.customer.view', ':id') }}'.replace(':id',
                            row.id);
                        /*Set the icon based on the status*/
                        var icon = '';
                        var color = '';

                        if (row.status === 'online') {
                            icon =
                                '<i class="fas fa-unlock" style="font-size: 15px; color: green; margin-right: 8px;"></i>';
                        } else if (row.status === 'offline') {
                            icon =
                                '<i class="fas fa-lock" style="font-size: 15px; color: red; margin-right: 8px;"></i>';
                        } else {
                            icon =
                                '<i class="fa fa-question-circle" style="font-size: 18px; color: gray; margin-right: 8px;"></i>';
                        }

                        return '<a href="' + viewUrl +
                            '" style="display: flex; align-items: center; text-decoration: none; color: #333;">' +
                            icon +
                            '<span style="font-size: 16px; font-weight: bold;">' + row
                            .fullname + '</span>' +
                            '</a>';
                    }
                },
                {
                    "data": "package.name",
                    "render": function(data, type, row) {
                        if (data) {
                            return data;
                        } else {
                            return '<span class="badge bg-secondary">N/A</span>';
                        }
                    }
                },
                {
                    "data": "amount"
                },
                {
                    "data": "phone"
                },
                {
                    "data": "pop.name"
                },
                {
                    "data": "area.name"
                },
                {
                    "data": "onu_type",
                    "render": function(data, type, row) {
                        if (data === 'customer') {
                            return '<span class="badge badge-success">Customer</span>';
                        } else if (data === 'company') {
                            return '<span class="badge badge-primary">Company</span>';
                        } else {
                            return '<span class="badge badge-secondary">Unknown</span>';
                        }
                    }
                },


            ],
            order: [
                [1, "desc"]
            ],
            dom: 'Bfrtip',
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>' +
           'rt' +
           '<"row"<"col-md-6"i><"col-md-6"p>>' +
           '<"row"<"col-md-12"B>>',
           lengthMenu: [[10, 25, 50,100,150,200, -1], [10, 25, 50,100,150,200, "All"]],
            "pageLength": 10,
            "buttons": [
                { extend: 'copy', text: 'Copy', className: 'btn btn-secondary btn-sm ' },
                { extend: 'csv', text: 'CSV', className: 'btn btn-secondary btn-sm ml-1' },
                { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm ml-1' },
                { extend: 'pdf', text: 'PDF', className: 'btn btn-danger btn-sm ml-1' },
                { extend: 'print', text: 'Print', className: 'btn btn-info btn-sm ml-1',title: "Customer Report - {{ date('Y-m-d') }}"},
            ],
        });
    });
</script>

@endsection
