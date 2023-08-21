<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Category;
use App\Models\Inventory\PartsModel;
use App\Models\Inventory\PartCategory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PartCsvProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $header;
    public $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header)
    {
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->data as $part) {
            $partData = array_combine($this->header,$part);
            dd($part);
            $a=PartsModel::where('name', $part['part_model'])->first();
            dd($a);
            Parts::create([
                'name' => $row['name'],
                'part_model_id' => PartsModel::where('name', $part['part_model'])->firstOrFail()->id, //->select('id')->first(), //$row['part_model_id'],
                'part_category_id' => PartCategory::where('name', $part['part_category'])->firstOrFail()->id,
                'product_category_id' => Category::where('name', $part['product_category'])->firstOrFail()->id,
                'code' => $row['code'],
                'unit' => $row['unit'],
                'type' => $row['type']=='General' ? 1 : 2,
                'status' => $row['status']=='Active' ? 1 : 0,
            ]);
        }
    }
}
