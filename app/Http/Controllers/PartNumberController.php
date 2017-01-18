<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PartNumber;

class PartNumberController extends Controller
{
    public function index()
    {
        if (\Gate::denies('partNumberView', PartNumber::class)) {
            abort(404);
        }

        return view('partnumber');
    }

    private function clean($string) {
        // source http://stackoverflow.com/questions/14114411/remove-all-special-characters-from-a-string
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
        $string = str_replace('-', '', $string);

        return $string;
    }

    private function query($request)
    {
        $searchQueries = preg_split('/\s+/', $request, -1, PREG_SPLIT_NO_EMPTY); 
        return $results = PartNumber::where(function ($q) use ($searchQueries) {
          foreach ($searchQueries as $value) {
            $q->orWhere(\DB::raw('REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                REPLACE(REPLACE(REPLACE(part_number, \'"\', \'\'),\' \', \'\'),
                \'.\', \'\'),\'?\', \'\'),\'`\', \'\'),\'<\', \'\'),\'=\', \'\'),\'{\',
                \'\'),\'}\',\'\'),\'[\', \'\'),\']\', \'\'),\'|\', \'\'),'.\DB::getPdo()->quote('\'').',
                \'\'),\':\', \'\'),\';\', \'\'),\'~\', \'\'),\'!\', \'\'),\'@\', \'\'),
                \'#\', \'\'),\'$\', \'\'),\'%\', \'\'),\'^\', \'\'),\'&\', \'\'),\'*\',
                \'\'),\'_\', \'\'),\'+\', \'\'),\',\', \'\'),\'/\', \'\'),\'(\', \'\'),\')\',
                \'\'),\'-\', \'\'),\'>\', \'\')'), 
                'like', '%'.$this->clean($value).'%');
          }
        });
    }

    public function search(Request $request)
    {
        if (\Gate::denies('partNumberSearch', PartNumber::class)) {
            abort(404);
        }

        if($request->q <> '' OR $request->q <> null){
            $results = $this->query($request->q)
                ->with('partnumbers')
                ->paginate(10);
        }else{
            $results = [];
        }
        
        $q = $request->q;
        return view('results', compact('results','q'));
    }

    public function excel(Request $request)
    {
        if (\Gate::denies('partNumberDownload', PartNumber::class)) {
            abort(404);
        }

        $content = $this->query($request->q)
            ->select(\DB::raw('
                    CONCAT(RIGHT(DATE_FORMAT(created_at,"%Y"),2),LPAD(item_code,5,0)),
                    part_number,
                    man_code,
                    man_name,
                    inc,
                    item_name,
                    short_desc,
                    po_text'
                )
            );
        $results = PartNumber::select(\DB::raw('
                "CATALOG NO",
                "PART NUMBER",
                "MAN CODE",
                "MANUFACTURER NAME",
                "INC",
                "ITEM NAME",
                "SHORT DESCRIPTION",
                "PO TEXT"
                ')
            )
            ->limit(1)
            ->union($content)
            ->get();

        \Exporter::make('Excel')
            ->load($results)
            ->stream(str_replace(' ', '_', strtolower($request->q)).'.xlsx');
    }
}
