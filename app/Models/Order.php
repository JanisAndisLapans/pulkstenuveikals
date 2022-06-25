<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['client_email', 'client_phone', 'order_address', 'zip_code'];

    public function products()
    {
        return $this->belongsToMany(Product::class,
            "product_order", foreignPivotKey: 'order_id', relatedPivotKey: 'product_id')->withPivot('count');
    }

    public function reviewVerificationCodes()
    {
        return $this->belongsToMany(ReviewVerificationCode::class);
    }

    public function reviews()
    {
        return $this->belongsToMany(Review::class);
    }

    public function getTableColumns() {
        //Atgriež visas kolonnas tabulā ar tipiem formātā, piem.,
        //['id' => 'nofill', 'name' => 'text', 'count' => 'integer']

        $builder = $this->getConnection()->getSchemaBuilder();
        $names = $builder->getColumnListing($this->getTable());
        $type_name = [];
        foreach($names as $name)
        {
            if(!in_array($name, $this->fillable))
            {
                $type_name[$name] = 'nofill';
            }
            else {
                $type_name[$name] = $builder->getColumnType($this->getTable(), $name);
            }
        }
        return $type_name;
    }
}
