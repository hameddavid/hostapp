<?php 

namespace App\GraphQL\Queries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Interfaces\ProductRepositoryInterface;


final readonly class ProductQueries
{
    // /** @param  array{}  $args */
    // public function __invoke(null $_, array $args)
    // {
    //     // TODO implement the resolver
    // }

    // private ProductRepositoryInterface $_product_repo;

    public function __construct(private ProductRepositoryInterface $_product_repo)
    {
        // $this->_product_repo = $product_repo;
    }
    

    public function index() 
    {
       
        return $this->_product_repo->getAllProducts();
    }

    public function getProductByID(null $_, array $prodId){
     
       
        return $this->_product_repo->getProductById($prodId['id']);
    }
}

