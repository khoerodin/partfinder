<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormattedItemCodeResetByYearTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER formatted_item_code_reset_by_year BEFORE INSERT ON part_numbers FOR EACH ROW
            BEGIN
              IF (
                SELECT COUNT(item_code)
                FROM part_numbers 
                WHERE item_name = NEW.item_name 
                AND short_desc = NEW.short_desc
                AND po_text = NEW.po_text) > 0 THEN 
                
                SET NEW.item_code = 
                  (SELECT item_code 
                  FROM part_numbers 
                  WHERE item_name = NEW.item_name 
                  AND short_desc = NEW.short_desc
                  AND po_text = NEW.po_text 
                  LIMIT 1);
              ELSE
                SET NEW.item_code = 
                  (SELECT IFNULL(MAX(item_code),0)+1
                  FROM part_numbers
                  WHERE DATE_FORMAT(created_at,"%Y") = DATE_FORMAT(now(),"%Y"));
              END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
