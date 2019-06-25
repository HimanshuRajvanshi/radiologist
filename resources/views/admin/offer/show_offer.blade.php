@extends('admin.layouts.app')

@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Offer List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Offer List</li>
      </ol>
    </section>

    <!-- Main content -->
     <section class="content">
      <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Tag Line</th>
                <th>Details</th>
                <th>Image</th>
                <th>Created Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($offers as $key => $offer)
            @php $key++;@endphp
            <tr>
                <td>{{$key}}</td>
                <td>{!! str_limit(strip_tags($offer->TagLine),25) !!}</td>
                <td>{!! str_limit(strip_tags($offer->Details),50) !!}</td>
                <td>{!! str_limit(strip_tags($offer->Image),20) !!}</td>
                <td>{{date('d-M-Y',strtotime($offer->CreatedAt))}}</td>
                <td>
                    @if($offer->IsActive ==0)
                        <button type="button" class="btn btn-primary" onclick='updateStatus("offer",1,{{$offer->OfferId}})'>Active</button>
                        <button type="button" class="btn btn-danger" style="cursor: not-allowed">Deactived</button>  
                    @elseif($offer->IsActive ==1)
                        <button type="button" class="btn btn-success" style="cursor: not-allowed">Actived</button>
                        <button type="button" class="btn btn-warning" onclick='updateStatus("offer",0,{{$offer->OfferId}})'>Deactive</button>      
                    @else
                        <button type="button" class="btn btn-primary" onclick='updateStatus("offer",1,{{$offer->OfferId}})'>Active</button>  
                        <button type="button" class="btn btn-warning" onclick='updateStatus("offer",0,{{$offer->OfferId}})'>Deactive</button>      
                    @endif
                </td>
            </tr>
            @endforeach 
        </tbody>
        <tfoot>
            <tr>
                <th>#</th>
                <th>Tag Line</th>
                <th>Details</th>
                <th>Image</th>
                <th>Created Date</th>
                <th>Status</th>
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
