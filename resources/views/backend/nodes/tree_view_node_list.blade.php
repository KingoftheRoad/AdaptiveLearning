@extends('backend.layouts.app')
    @section('content')
    @php
    $permissions = [];
    $user_id = auth()->user()->id;
    if($user_id){
        $module_permission = App\Helpers\Helper::getPermissions($user_id);
        if($module_permission && !empty($module_permission)){
            $permissions = $module_permission;
        }
    }else{
        $permissions = [];
    }
    @endphp
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.nodes.node_detail')}}</h2>
								<div class="btn-sec">
                                    @if (in_array('node_management_create', $permissions))
                                    <a href="{{ route('nodes.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.nodes.add_new_node')}}</a>
                                    @endif
                                    <a href="{{ route('nodes.tree-view-list') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.nodes.tree_view')}}</a>
                                    <a href="{{ route('nodes.index') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.nodes.list_view')}}</a>
								</div>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
					@if(session()->has('success_msg'))
					<div class="alert alert-success">
						{{ session()->get('success_msg') }}
					</div>
					@endif
					@if(session()->has('error_msg'))
					<div class="alert alert-danger">
						{{ session()->get('error_msg') }}
					</div>
					@endif
                    <div class="row">
                        <div id="node_list_tree_view" class="tree-demo">
                            <?php echo $nodelist;?>
                        </div>
                    </div>
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
        <!-- Start Display Treeview Js -->
        <script src="{{ asset('js/jstree/dist/jstree.min.js')}}"></script>
        <!-- End Display Treeview Js -->
        <script>
            $(document).ready(function () {
                $('#node_list_tree_view').jstree({
                    "core" : {
                        "themes" : {
                            "responsive": true
                        }
                    },
                    "types" : {
                        "default" : {
                            "icon" : "fa fa-folder icon-state-warning icon-lg"
                        },
                        "file" : {
                            "icon" : "fa fa-file icon-state-warning icon-lg"
                        }
                    },
                    "plugins": ["types"]
                });
            });
        </script>
@endsection