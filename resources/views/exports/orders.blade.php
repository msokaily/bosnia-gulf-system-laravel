@php
    $headCellBg = '#cccccc';
    $theadStyle = 'text-align: center; border: 1px solid #424242; padding: 5px;';
@endphp
<table id="kt_table_accounts" class="table table-row-bordered">
    <thead>
        <tr>
            <td colspan="15" style="{{$theadStyle}}" bgcolor="{{$headCellBg}}"><b>{{ $date->format('F Y') }}</b></td>
        </tr>
        <tr>
            <th style="{{$theadStyle}} min-width: 50px; width: 50px;" bgcolor="{{$headCellBg}}">Red. br.:</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Ime klijenta:</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Datum dolaska:</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Datum odlaska:</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Vozilo:</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Cijena (€):</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Kapara (€):</th>
            <th style="{{$theadStyle}} min-width: 200px; width: 200px;" bgcolor="{{$headCellBg}}" colspan="3">Vozač - trošk. + plata ( - المصاريف - الراتب):</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Smještaj:</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Zarada (€):</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">UBERI (€):</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}">Dodatni troškovi:</th>
            <th style="{{$theadStyle}} min-width: 100px; width: 100px;" bgcolor="{{$headCellBg}}"></th>
        </tr>
        <tr>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">الرقم</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">اسم العميل</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">تاريخ الوصول</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">تاريخ المغادرة</th>
            <th style="{{$theadStyle}} min-width: 150px; width: 150px;" bgcolor="{{$headCellBg}}">السيارة</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">السعر المدفوع بالكاش</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">العربون</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">Vozač:</th>
            <th style="{{$theadStyle}} min-width: 150px; width: 150px;" bgcolor="{{$headCellBg}}">Troškovi:</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">Plata:</th>
            <th style="{{$theadStyle}} min-width: 150px; width: 150px;" bgcolor="{{$headCellBg}}">السكن</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">الأرباح</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">أرباح اوبري</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}">مصاريف أخرى</th>
            <th style="{{$theadStyle}}" bgcolor="{{$headCellBg}}"></th>
        </tr>
    </thead>
    <tbody>
    <?php $x = 1; ?>
    @forelse($items as $item)
        <tr class="odd gradeX" id="tr-{{ $item->id }}">
            <td>{{$x++}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->arrive_at}}</td>
            <td>{{$item->leave_at}}</td>
            <td>
                @foreach ($item->cars as $i => $car)
                    <div>{{$car->product->name}} -</div>
                    {{-- @if(count($item->cars) < $i) --}}
                        <br>
                    {{-- @endif --}}
                @endforeach
            </td>
            <td>{{ decorate_numbers($item->paid_eur) }} &euro;</td>
            <td>{{ decorate_numbers($item->down_payment) }} &euro;</td>
            <td>{{ decorate_numbers($item->deposit) }} &euro;</td>
            <td>{{ $item->airline }} {{ $item->arrive_time }}</td>
            <td></td>
            <td>
                @foreach ($item->accommodations as $i => $accommodation)
                    <div>{{$accommodation->product->name}} -</div>
                    {{-- @if(count($item->accommodations) < $i) --}}
                        <br>
                    {{-- @endif --}}
                @endforeach
            </td>
            <td></td>
            <td></td>
            <td>{{ $item->user->name }}</td>
            <td>{{ $item->phone }}</td>
        </tr>
    @empty
    @endforelse
    </tbody>
</table>