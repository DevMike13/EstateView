<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PHBarangays;
use App\Models\PHCities;
use App\Models\PHProvinces;
use App\Models\PHRegions;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getRegions(Request $request){
        $search = $request->input('search');
        $selected = $request->input('selected');

        if ($search) {
            $regions = PHRegions::where('region_description', 'like', '%' . $search . '%')->get();
        } elseif ($selected) {

            $selectedRegion = PHRegions::where('region_description', $selected)->get();

            return response()->json($selectedRegion);
            
        } else {
            $regions = PHRegions::all();
        }

        return response()->json($regions);
    }

    public function getProvinces(Request $request){
        $search = $request->input('search');
        $selected = $request->input('selected');
        
        if ($search) {
            $provinces = PHProvinces::where('province_description', 'like', '%' . $search . '%')->get();
        } elseif ($selected) {

            $selectedProvince = PHProvinces::where('province_description', $selected)->get();
            return response()->json($selectedProvince);
            
        } else {
            $provinces = PHProvinces::take(10)->get();
        }

        return response()->json($provinces);
    }

    public function getProvincesByRegion(Request $request, $regionCode){
        $search = $request->input('search');
        $selected = $request->input('selected');
        
        if ($search && $regionCode) {
            $provinces = PHProvinces::where('province_description', 'like', '%' . $search . '%' )
                ->where('region_code', $regionCode)
                ->get();
        } elseif ($selected && $regionCode) {

            $selectedProvince = PHProvinces::where('province_description', $selected)
                ->where('region_code', $regionCode)
                ->get();
            return response()->json($selectedProvince);
            
        } else {
            $provinces = PHProvinces::where('region_code', $regionCode)->get();
        }

        return response()->json($provinces);
    }

    public function getMunicipalities(Request $request){
        $search = $request->input('search');
        $selected = $request->input('selected');
        
        if ($search) {
            $municipalities = PHCities::where('city_municipality_description', 'like', '%' . $search . '%')->get();
        } elseif ($selected) {

            $selectedMunicipality = PHCities::where('city_municipality_description', $selected)->get();
            return response()->json($selectedMunicipality);
            
        } else {
            $municipalities = PHCities::take(10)->get();
        }

        return response()->json($municipalities);
    }

    public function getMunicipalitiesByProvince(Request $request, $provinceCode){
        $search = $request->input('search');
        $selected = $request->input('selected');
        
        if ($search && $provinceCode) {
            $municipalities = PHCities::where('city_municipality_description', 'like', '%' . $search . '%' )
                ->where('province_code', $provinceCode)
                ->get();
        } elseif ($selected && $provinceCode) {

            $selectedMunicipality = PHCities::where('city_municipality_description', $selected)
                ->where('province_code', $provinceCode)
                ->get();
            return response()->json($selectedMunicipality);
            
        } else {
            $municipalities = PHCities::where('province_code', $provinceCode)->get();
        }

        return response()->json($municipalities);
    }

    public function getBarangays(Request $request){
        $search = $request->input('search');
        $selected = $request->input('selected');
        
        if ($search) {
            $barangays = PHBarangays::where('barangay_description', 'like', '%' . $search . '%')->get();
        } elseif ($selected) {

            $selectedBarangay = PHBarangays::where('barangay_description', $selected)->get();
            return response()->json($selectedBarangay);
            
        } else {
            $barangays = PHBarangays::take(10)->get();
        }

        return response()->json($barangays);
    }
    public function getBarangaysByMunicipality(Request $request, $municipalityCode){
        $search = $request->input('search');
        $selected = $request->input('selected');
        
        if ($search && $municipalityCode) {
            $barangays = PHBarangays::where('barangay_description', 'like', '%' . $search . '%')
                ->where('city_municipality_code', $municipalityCode)
                ->get();
        } elseif ($selected && $municipalityCode) {

            $selectedBarangay = PHBarangays::where('barangay_description', $selected)
                ->where('city_municipality_code', $municipalityCode)
                ->get();
            return response()->json($selectedBarangay);
            
        } else {
            $barangays = PHBarangays::where('city_municipality_code', $municipalityCode)->get();
        }

        return response()->json($barangays);
    }
}
