<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function PHPUnit\Framework\isNull;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/products",
     *      operationId="index",
     *      tags={"Product"},
     *      summary="Get all products",
     *      description="Get all products",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Get All products"),
     *              @OA\Property(property="data", type="array",
     *              @OA\Items(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Product 1"),
     *              @OA\Property(property="price", type="integer", example=10000),
     *              @OA\Property(property="description", type="string", example="Description product 1"),
     *              @OA\Property(property="image", type="string", example="image.jpg"),
     *              )
     *            )
     *        )
     *     ),
     * )
     */

    public function index()
    {
        // $products = Product::all();

        // paginate HATEOAS
        $products = Product::paginate(5);

        // $data = [
        //     'status' => 200,
        //     'message' => 'Data produk berhasil diambil',
        //     'data' => $products
        // ];
        return response()->json($products, Response::HTTP_OK);
    }


    /**
     * @OA\Get(
     *      path="/api/products/{id}",
     *      operationId="show",
     *      tags={"Product Sendiri"},
     *      summary="Get product by id",
     *      description="Get product by id",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(response=200,description="Success",
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    // Detail Product
    public function show($id)
    {
        $products = Product::find($id);
        if (is_null($products)) {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data produk tidak ditemukan'
            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Data produk berhasil ditemukan',
            'data' => $products
        ];
        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *      path="/api/products",
     *      operationId="store",
     *      tags={"Product"},
     *      summary="Create new product",
     *      description="Create new product",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                type="object",
     *                required={"name", "price", "description", "image"},
     *                @OA\Property(property="name", type="string", example="Product 1"),
     *                @OA\Property(property="price", type="integer", example="10000"),
     *                @OA\Property(property="description", type="string", example="Description product 1"),
     *                @OA\Property(property="image", type="string", format="binary"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    // Create Product

    public function store(Request $request)
    {
        // Validasi Request
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'description' => 'required|string',
            'image' => 'image|required|max:2048|mimes:png,jpg,jpeg'
        ]);

        $input = $request->all();

        // Logika upload gambar
        if ($image = $request->file('image')) {
            $target = 'assets/images/';
            $products_img = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($target, $products_img);
            $input['image'] = $products_img;
        }

        // Input ke DB
        Product::create($input);

        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => 'Data produk berhasil ditambahkan',
            'data' => $input
        ];
        return response()->json($data, Response::HTTP_CREATED);
    }

    // Update Product

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if ($product) {
            // Validasi Request
            $request->validate([
                'name' => 'string|max:255',
                'price' => 'integer',
                'description' => 'string',
                // 'image' => 'image|max:2048|mimes:png,jpg,jpeg'
            ]);

            $input = $request->all();

            // Logika upload gambar
            if ($image = $request->file('image')) {
                $target = 'assets/images/';
                // Jika ada image
                unlink($target . $product->image);
                $products_img = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($target, $products_img);
                $input['image'] = $products_img;
            } else {
                $input['image'] = $product->image;
            }

            // Update Data ke DB
            $product->update($input);

            $data = [
                'status' => Response::HTTP_CREATED,
                'message' => 'Data produk berhasil diupdate',
                'data' => $product
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } else {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data produk tidak ditemukan'
            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }
    }

    // Delete Product

    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);

        if ($product) {

            $target = 'assets/images/';
            // Jika ada image
            unlink($target . $product->image);
            $product->delete();

            $data = [
                'status' => Response::HTTP_OK,
                'message' => 'Data produk berhasil dihapus',
                'data' => $product
            ];
            return response()->json($data, Response::HTTP_OK);
        } else {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data produk tidak ditemukan'
            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }
    }
}
