<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
Use \Carbon\Carbon;

class PharmacyPrescriptionController extends Controller
{
	public function search(Request $req){
		if($req->search){
		$contactno = (int)$req->search;
		$data = DB::table('prescriptions')
	    ->join('doctors', 'doctors.id', '=', 'prescriptions.doctorid')
	    ->where('prescriptions.patientcontactno',$contactno)
		->orderBy('prescriptions.date')
		->select('prescriptions.*','prescriptions.id as preid', 'doctors.name as dname', 'doctors.*')
	    ->get();
			if($data){
				$i = 0;
				foreach($data as $value => $search){
					echo '
					      <tr>
			                  <td colspan="3">' . $search->dname . '</td>
			                  <td colspan="2"></td>
					      </tr>
					      <tr>
			                  <td colspan="3">' . $search->designation . '</td>
			                  <td colspan="2"></td>
					      </tr>
					      
					      <tr>
					      	  <td colspan="5"><hr></td>
					      </tr>
					      <tr>
					          <td>Name: ' . $search->patientname . '</td>
					          <td>Age: ' . $search->patientage . '</td>
					          <td>Sex: ' . $search->patientgender . '</td>
					          <td>Contactno: ' . $search->patientcontactno . '</td>
			                  <td>Date: ' . $search->date . '</td>				                   		           
					      </tr>
					      <tr>
					      	  <td colspan="5"><hr></td>
					      </tr>';
					$data1 = DB::table('prescriptionitems')
					->join('medicines', 'medicines.id', '=', 'prescriptionitems.medicineid')
					->join('types', 'types.id', '=', 'medicines.typeid')
				    ->where('prescriptionitems.prescriptionid',$search->preid)
				    ->select('prescriptionitems.*','medicines.*','types.name as typename')
				    ->get();
				    foreach($data1 as $value => $search1){
					    echo '<tr>
						           <td colspan="2">' . substr($search1->typename,0,3) . ':' . substr($search1->name,0,40) . '
						                             <sub>' .$search1->mg . '</sub></td>
						           <td>' .$search1->morning. '+' .$search1->afternoon. '+' .$search1->evening. '</td>
				                   <td>' . $search1->days . 'Days</td>		
				                   <td>' . $search1->meal . '</td>			                   		           
						      </tr>';
					}
					echo '<tr><td style="background-color:white;" colspan="6"><br></td></tr>';
					echo '<tr><td style="background-color:white;" colspan="6"><br></td></tr>';
					echo '<tr><td colspan="6" align="center">
							<input type="button" value="Make Bill" onclick="javascript:makeBill('.$search->preid.','. $contactno .');"  
					           	    style="background-color: blue; color: white; text-align: center;">
					      </td></tr>"';
					echo '<tr><td style="background-color:white;" colspan="6"><br></td></tr>';
                    echo '<tr><td style="background-color:#f2f2f2; height:50px" colspan="6"><br></td></tr>';
				}
			}
		}
	} 
}
