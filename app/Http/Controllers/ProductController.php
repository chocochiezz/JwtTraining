<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function PHPUnit\Framework\isNull;

class ProductController extends Controller
{
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
