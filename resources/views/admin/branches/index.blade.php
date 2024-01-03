@extends('layouts.admin')

@section('contentHeader')
بينانت الفروع
@endsection

@section('content')
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title card_title_center">  بيانات الفروع </h3>
            <a name="" id="" class="btn btn-primary text-center" href="{{ route('finance_calender.create') }}" role="button">اضافة جديد</a>
         </div>
        <div class="card-body">
            @if(@isset($data) and !@empty($data) )
            <table id="example2" class="table table-bordered table-hover">
               <thead class="custom_thead">
                  <th> كود الفرع</th>
                  <th> الاسم</th>
                  <th>  العنوان </th>
                  <th> الهاتف</th>
                  <th>  الايميل</th>
                  <th>  حالة التفعيل</th>
                  <th>  الاضافة بواسطة</th>
                  <th>  التحديث بواسطة</th> 
               </thead>
               <tbody>
                  @foreach ( $data as $info )
                  <tr>
                     <td> {{ $info->FINANCE_YR }} </td>
                     <td> {{ $info->FINANCE_YR_DESC }} </td>
                     <td> {{ $info->start_date }} </td>
                     <td> {{ $info->end_date }} </td>
                     <td> 
                        @if($info->is_open==1)
                        مفتوحة
                        @else
                        مغلقة
                        @endif
                     </td>
                     <td> {{ $info->added->name }} </td>
                     <td> 
                        @if($info->updated_by>0)
                        {{ $info->updated_by->name }} 
                        @else
                        لايوجد
                        @endif
                     </td>
                     <td>
                        <a  href="{{ route('finance_calender.do_open',$info->id) }}" class="btn btn-primary btn-sm">فتح سنة</a>
                        <a  href="{{ route('finance_calender.edit',$info->id) }}" class="btn btn-success btn-sm">تعديل</a>
                        <a  href="{{ route('finance_calender.destroy',$info->id) }}" class="btn are_you_sure  btn-danger btn-sm">حذف</a>
                        <button class="btn btn-info btn-sm show_year_months" data-id="{{ $info->id }}">عرض الشهور</button>
                     </td>
                   </tr>
                  @endforeach
               </tbody>
            </table>
            @else
            <p class="bg-danger text-center"> عفوا لاتوجد بيانات لعرضها</p>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="show_year_monthsModal">
   <div class="modal-dialog modal-xl">
     <div class="modal-content bg-info">
       <div class="modal-header">
         <h4 class="modal-title">عرض الشهور المالية</h4>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span></button>
       </div>
       <div class="modal-body" id="show_year_monthsModalBody">
       </div>
       <div class="modal-footer justify-content-between">
         <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
         <button type="button" class="btn btn-outline-light">Save changes</button>
       </div>
     </div>
     <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
 </div>
@endsection

@section('script')
<script>
   $(document).ready(function(){
      $(document).on('click', '.show_year_months', function(){
         var id=$(this).data('id');
         jQuery.ajax({
            url: '{{ route('finance_calender.show_year_months') }}',
            type: 'post',
            "dataType":'html',
            cache: false,
            data: { "_token": '{{ csrf_token() }}', 'id':id },
            success:function(data){
               $('#show_year_monthsModalBody').html(data);
               $('#show_year_monthsModal').modal('show')

            },
            error:function(){
            
            }
         })
      })
   });
</script>
@endsection