<div class="card-body">
    @if(@isset($finance_calender) and !@empty($finance_calender) )
    <table id="example2" class="table table-bordered table-hover">
       <thead class="custom_thead">
          <th> اسم الشهر </th>
          <th> الانجليزي</th>
          <th>  تاريخ البداية</th>
          <th>  تاريخ النهاية</th>
          <th> عدد الايام </th>
          <th>  الاضافة بواسطة</th>

       </thead>
       <tbody>
          @foreach ( $finance_calender as $info )
          <tr>
             <td> {{ $info->month->name }} </td>
             <td> {{ $info->month->name_en}} </td>
             <td> {{ $info->START_DATE_M }} </td>
             <td> {{ $info->END_DATE_M }} </td>
             <td> {{ $info->number_of_days}}</td>
             <td> {{ $info->added->name }} </td>

           </tr>
          @endforeach
       </tbody>
    </table>
    @else
    <p class="bg-danger text-center"> عفوا لاتوجد بيانات لعرضها</p>
    @endif
</div>