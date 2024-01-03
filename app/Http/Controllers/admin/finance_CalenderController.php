<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Finance_calender;
use Illuminate\Http\Request;
use App\Http\Requests\Finance_calenderRequest;
use App\Http\Requests\Finance_calendersUpdate;
use App\Models\Month;
use DateInterval;
use DatePeriod;
use DateTime;
use App\Models\Finance_cln_periods;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class finance_CalenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = get_columns_where_pagination(Finance_calender::class, ['*'], ['company_code'=>auth()->user()->company_code], 'FINANCE_YR', 'DESC', 15);
        
        return view("admin.Finance_calender.index", ["data"=> $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.Finance_calender.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Finance_calenderRequest $request)
    {
        try{
            DB::beginTransaction();
                $dataToInsert['FINANCE_YR']=$request->FINANCE_YR;
                $dataToInsert['FINANCE_YR_DESC']=$request->FINANCE_YR_DESC;
                $dataToInsert['start_date']=$request->start_date;
                $dataToInsert['end_date']=$request->end_date;
                $dataToInsert['added_by']=auth()->user()->id;
                $dataToInsert['company_code']=auth()->user()->company_code;
                $newFinanceCalender = Finance_calender::create($dataToInsert);
                if (!$newFinanceCalender) {
                    throw new \Exception("Failed to create Finance_calender record");
                }

                if($newFinanceCalender){
                $financeCalenderId = $newFinanceCalender->id;
                $startDate=new DateTime($request->start_date);
                $endDate=new DateTime($request->end_date);
                $dareInterval=new DateInterval('P1M');
                $datePerioud=new DatePeriod($startDate,$dareInterval,$endDate);

                foreach($datePerioud as $date){
                    $dataMonth['finance_calenders_id']=$financeCalenderId;
                    $Montname_en=$date->format('F');
                    $dataParentMonth=Month::select("id")->where(['name_en'=>$Montname_en])->first();
                    if (!$dataParentMonth) {
                        throw new \Exception("Failed to find Month with name_en: $Montname_en");
                    }
                    $dataMonth['MONTH_ID']=$dataParentMonth['id'];
                    $dataMonth['FINANCE_YR']=$dataToInsert['FINANCE_YR'];
                    $dataMonth['START_DATE_M']=date('Y-m-01',strtotime($date->format('Y-m-d')));
                    $dataMonth['END_DATE_M']=date('Y-m-t',strtotime($date->format('Y-m-d')));
                    $dataMonth['year_and_month']=date('Y-m',strtotime($date->format('Y-m-d')));
                    $datediff=strtotime( $dataMonth['END_DATE_M'])-strtotime( $dataMonth['START_DATE_M']);
                    $dataMonth['number_of_days']=round($datediff/(60*60*24))+1;
                    $dataMonth['company_code']=auth()->user()->company_code;
                    $dataMonth['updated_at']=date("Y-m-d H:i:s");
                    $dataMonth['created_at']=date("Y-m-d H:i:s");
                    $dataMonth['added_by']=auth()->user()->id;
                    $dataMonth['updated']=auth()->user()->id;
                    $dataMonth['start_date_for_pasma']=date('Y-m-01',strtotime($date->format('Y-m-d')));
                    $dataMonth['end_date_for_pasma']=date('Y-m-t',strtotime($date->format('Y-m-d')));
                    Finance_cln_periods::insert($dataMonth);
                
                
                };
               DB::commit();
            return redirect()->route('finance_calender.index')->with("success","تم اضافة سنة مالية");
        }
         //echo var_dump($flag);
    }
        catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Finance_calender $finance_calender)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Finance_calender::select('*')->where(['id'=>$id])->first();
        if(empty($data)) return redirect()->back()->with("error"," عفوا حدث خطأ");
        if ($data['is_open'] != 0) return redirect()->back()->with('error','لا يمكن تعديل سنة مالية لانها مازالت مفتوحة');
        return view('admin.Finance_calender.update',['data'=>$data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Finance_calendersUpdate  $request)
    {
        try {
            $data = Finance_calender::select("*")->where(['id' => $id])->first();
            if (empty($data)) {
            return redirect()->back()->with(['error' => ' عفوا حدث خطأ ']);
            }
            if ($data['is_open'] != 0) {
            return redirect()->back()->with(['error' => ' عفوا لايمكن تعديل السنة المالية في هذه الحالة'])->withInput();
            }
            $validator=Validator::make($request->all(),[
            'FINANCE_YR'=>['required',Rule::unique('finance_calenders')->ignore($id) ],
            ]);    
            if($validator->fails()){
            return redirect()->back()->with(['error' => ' عفوا اسم السنة المالية مسجل من قبل'])->withInput();
            }  
            
            $data = Finance_calender::select('*')->where(['id'=>$id])->first();
            if(empty($data)) return redirect()->back()->with("error"," عفوا حدث خطأ");
            if ($data['is_open'] != 0) return redirect()->back()->with('error','لا يمكن تعديل سنة مالية لانها مازالت مفتوحة')->withInput();
            
            DB::beginTransaction();
            $dataToUpdate['FINANCE_YR'] = $request->FINANCE_YR;
            $dataToUpdate['FINANCE_YR_DESC'] = $request->FINANCE_YR_DESC;
            $dataToUpdate['start_date'] = $request->start_date;
            $dataToUpdate['end_date'] = $request->end_date;
            $dataToUpdate['updated_by'] = auth()->user()->id;
            $falg = Finance_calender::where(['id' => $id])->update($dataToUpdate);
            if ($falg) {
            if ($data['start_date'] != $request->start_date or $data['end_date'] != $request->end_date) {
            $flagDelete = Finance_cln_periods::where(['finance_calenders_id' => $id])->delete();
            if ($flagDelete) {
            $startDate = new DateTime($request->start_date);
            $endDate = new DateTime($request->end_date);
            $dareInterval = new DateInterval('P1M');
            $datePerioud = new DatePeriod($startDate, $dareInterval, $endDate);
            foreach ($datePerioud as $date) {
            $dataMonth['finance_calenders_id'] = $id;
            $Montname_en = $date->format('F');
            $dataParentMonth = Month::select("id")->where(['name_en' => $Montname_en])->first();
            $dataMonth['MONTH_ID'] = $dataParentMonth['id'];
            $dataMonth['FINANCE_YR'] = $dataToUpdate['FINANCE_YR'];
            $dataMonth['START_DATE_M'] = date('Y-m-01', strtotime($date->format('Y-m-d')));
            $dataMonth['END_DATE_M'] = date('Y-m-t', strtotime($date->format('Y-m-d')));
            $dataMonth['year_and_month'] = date('Y-m', strtotime($date->format('Y-m-d')));
            $datediff = strtotime($dataMonth['END_DATE_M']) - strtotime($dataMonth['START_DATE_M']);
            $dataMonth['number_of_days'] = round($datediff / (60 * 60 * 24)) + 1;
            $dataMonth['com_code'] = auth()->user()->com_code;
            $dataMonth['updated_at'] = date("Y-m-d H:i:s");
            $dataMonth['created_at'] = date("Y-m-d H:i:s");
            $dataMonth['added_by'] = auth()->user()->id;
            $dataMonth['updated_by'] = auth()->user()->id;
            $dataMonth['start_date_for_pasma'] = date('Y-m-01', strtotime($date->format('Y-m-d')));
            $dataMonth['end_date_for_pasma'] = date('Y-m-t', strtotime($date->format('Y-m-d')));
            Finance_cln_periods::insert($dataMonth);
            }
            }
            }
            }
        DB::commit();
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $data = Finance_calender::find($id);
            if(empty($data)) return redirect()->back()->with("error"," عفوا حدث خطأ");
            if ($data['is_open'] != 0) return redirect()->back()->with('error','لا يمكن حذف سنة مالية لانها مازالت مفتوحة');
            $data->delete();
            return redirect()->route('finance_calender.index')->with('success','تم حذف السنة المالية '.$data['FINANCE_YR'].'');
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage())->withInput();
        }
    }

    public function do_open($id)
    {
        try{
            $data = Finance_calender::find($id);
            if(empty($data)) return redirect()->back()->with("error"," عفوا حدث خطأ المعطيات فارغة");
            if ($data['is_open'] != 0) return redirect()->back()->with('error','لا يمكن فتح السنة المالية لانها مازالت مفتوحة');
            $checkOpenYear = Finance_calender::select('*')->where(['is_open'=>1])->first();
            if(!empty($checkOpenYear)) return redirect()->back()->with('error','لا يمكن فتح السنة المالية لان هنالك سنة مازالت مفتوحة');
            $dataToUpdate["is_open"] = 1;
            $dataToUpdate["updated_by"] = auth()->user()->id;
            $data->update($dataToUpdate);
            return redirect()->route('finance_calender.index')->with('success','تم فتح السنة المالية '.$data['FINANCE_YR'].'');
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage())->withInput();
        }
    }

    public function show_year_months(Request $request){
        if($request->ajax()){
            $finance_calender = Finance_cln_periods::select('*')->where(['finance_calenders_id'=>$request->id])->get();
            return view('admin.Finance_calender.show_year_months', ["finance_calender"=>$finance_calender]);
        };

    }
}
