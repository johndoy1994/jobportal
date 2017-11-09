<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CRUDExampleController extends Controller
{

	// Listing 
    public function getListing() {

    	$items = Item::paginate(15);
    	return view('backend.crud.listing', [
    		"items" => $items
		]);

    }

    // New Item
    public function getNewItem(Request $request) {
    	return view('backend.crud.new-item');
    }

    public function postNewItem(Request $request) {

    	$this->validate($request, [
    		'name' => 'required|min:5',
    		'price' => 'required|numeric'
		]);

		$item = new Item();
		$item->name = $request["name"];
		$item->price = $request["price"];
		if($item->save()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "New item successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your item, try again"
			]);
		}

    }	

    // Edit Item
    public function getEditItem(Request $request, Item $item) {
    	return view('backend.crud.edit-item',[
    		"item"=>$item
		]);
    }

    public function postEditItem(Request $request, Item $item) {
		$this->validate($request, [
    		'name' => 'required|min:5',
    		'price' => 'required|numeric'
		]);

		$item->name = $request["name"];
		$item->price = $request["price"];
		if($item->update()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "Item successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your item, try again"
			]);
		}
    }

    // Delete Item
	public function getDeleteItem(Request $request, Item $item) {
		return view('backend.crud.delete-item', ['item'=>$item]);
    }	

    public function postDeleteItem(Request $request, Item $item) {

    	if($item->delete()) {
			return redirect()->route('admin-crud')->withInput(Input::all())->with([
				'success_message' => "Item successfully deleted!"
			]);
		} else {
			return redirect()->route('admin-crud')->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your item, try again!"
			]);
		}

    }    
}
