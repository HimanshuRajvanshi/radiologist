@extends('admin.layouts.app')

@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Blog List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Blog List</li>
      </ol>
    </section>

    <!-- Main content -->
     <section class="content">
      <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Short Description</th>
                <th>Long Description</th>
                <th>Url</th>
                <th>Created Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($blogs as $key => $blog)
            @php $key++;@endphp
            <tr>
                <td>{{$key}}</td>
                <td>{!! str_limit(strip_tags($blog->Description),25) !!}</td>
                <td>{!! str_limit(strip_tags($blog->LongDescription),50) !!}</td>
                <td>{!! str_limit(strip_tags($blog->Url),20) !!}</td>
                <td>{{date('d-M-Y',strtotime($blog->CreatedAt))}}</td>
                <td>
                    @if($blog->IsActive ==0)
                        <button type="button" class="btn btn-primary" onclick='updateStatus("blog",1,{{$blog->BlogId}})'>Active</button>
                        <button type="button" class="btn btn-danger" style="cursor: not-allowed">Deactived</button>  
                    @elseif($blog->IsActive ==1)
                        <button type="button" class="btn btn-success" style="cursor: not-allowed">Actived</button>
                        <button type="button" class="btn btn-warning" onclick='updateStatus("blog",0,{{$blog->BlogId}})'>Deactive</button>      
                    @else
                        <button type="button" class="btn btn-primary" onclick='updateStatus("blog",1,{{$blog->BlogId}})'>Active</button>  
                        <button type="button" class="btn btn-warning" onclick='updateStatus("blog",0,{{$blog->BlogId}})'>Deactive</button>      
                    @endif
                </td>
            </tr>
            @endforeach 
        </tbody>
        <tfoot>
            <tr>
                <th>#</th>
                <th>Short Description</th>
                <th>Long Description</th>
                <th>Url</th>
                <th>Created Date</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>



    </section>    
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );


</script>
@endsection		
