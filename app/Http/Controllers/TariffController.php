<?php

namespace App\Http\Controllers;

use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    //Получить все тарифы
    public function getAllTariff()
    {
        $tariff = Tariff::where('deleted', 0)->get();
        foreach ($tariff as $element) {
            $element['date_open_ru'] = $this->embalishDate($element['date_open']);
            $element['date_close_ru'] = $this->embalishDate($element['date_close']);
        }
        return view('admin/tariff/tariff', ['data' => $tariff]);
    }

    //Удаление тарифа по его ID
    public function deleteTariff(Request $request)
    {
        $tariff = Tariff::find($request->id)->update(['deleted' => 1]);

        return response()->json(['status' => 'ok']);
    }

    //Добавление/редактирование тарифов
    public function addUpdateTariff($id)
    {
        $type = 'edit';
        $data = '';

        if ($id == -1)
            $type = 'add';

        if ($type == 'edit') {
            $data = Tariff::find($id);
        }
        return view('admin/tariff/add-update-tariff', ['type' => $type, 'data' => $data, 'id' => $id]);
    }

    public function addUpdateTariffSubmit($id, Request $request)
    {
        $name = $request->input('name');
        $date_open = $request->input('date_open');
        $date_close = $request->input('date_close');
        $errorMessages = [];
        $data = [];

        $type = 'edit';
        if ($id == -1)
            $type = 'add';

        if ($type == 'add') {
            $data['name'] = $name;
            $data['date_open'] = $date_open;
            $data['date_close'] = $date_close;
        }

        $tariffCheckName = Tariff::where('deleted', 0)->where('name', $name)->where('id', '!=', $id)->first();
        if (!empty($tariffCheckName)) {
            $errorMessages[] = 'Тариф с данным назваением уже существует!';
        } elseif (!$name || !$date_open || !$date_close) {
            $errorMessages[] = 'Не все поля были указаны!';
        } else {
            $tariff = new Tariff;
            if ($type == 'edit') {
                $tariff = Tariff::find($id);
                $tariff->update([
                    'name' => $name,
                    'date_open' => $date_open,
                    'date_close' => $date_close
                ]);
                $data = $tariff;
            } else {
                $tariff = new Tariff([
                    'name' => $name,
                    'date_open' => $date_open,
                    'date_close' => $date_close
                ]);
            }
            $tariff->save();

            if ($type == 'add')
                return redirect('/admin/tariff');
        }

        if ($type == 'edit' && !empty($errorMessages))
            $data = Tariff::find($id);

        return view('admin/tariff/add-update-tariff', ['type' => $type, 'data' => $data, 'id' => $id, 'error_messages' => $errorMessages]);
    }
}
