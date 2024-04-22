@extends('layout.mainlayout', ['activePage' => 'medicines'])

@section('content')
<section class="section">
<div class="pt-14 border-b border-white-light mb-10 pb-10">
    <h1 class="font-fira-sans font-semibold text-5xl text-center leading-10">{{__('Our Medicines')}}</h1>
    <div class="p-5">
        <p class="font-fira-sans font-normal text-lg text-center leading-5 text-gray">{{__('Explore a variety of medicines')}}</p>
    </div>
    <form id="searchForm" method="post" action="{{ url('medicines') }}">
        @csrf
        <div class="flex justify-center space-x-5">
            <div class="relative">
                <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="search" name="search_val" id="default-search" class="block p-2 pl-10 text-sm text-gray-100 bg-white-50 border border-white-light h-12" placeholder="{{__('Search Medicine...')}}" required>
            </div>
            <input type="hidden" name="from" value="js">
            <button type="button" onclick="searchMedicine()" class="text-white bg-primary text-center px-6 py-2 text-base font-normal leading-5 font-fira-sans h-12">
                <i class="fa-solid fa-magnifying-glass"></i> {{__('Search')}}
            </button>
            <button type="reset" class="text-white bg-primary text-center px-4 py-4 text-base font-normal leading-5 font-fira-sans h-12">
                {{__('Clear Search')}}
            </button>
        </div>
    </form>
</div>


<div class="card">
    <div class="card-body">
        <div class="table-responsive mt-10">
            <table class="table w-100 datatable text-center">
                <thead>
                    <tr>
                        <th>{{__('Name')}}</th>
                        <th>{{__('Number of Medicine')}}</th>
                        <th>{{__('Description')}}</th>
                        <th>{{__('Works')}}</th>
                        <th>{{__('Prescription Required')}}</th>
                        <th>{{__('Meta Info')}}</th>
                        <th>{{__('Code')}}</th>
                        <th>{{__('DCI1')}}</th>
                        <th>{{__('Dosage1')}}</th>
                        <th>{{__('Unit Dosage1')}}</th>
                        <th>{{__('Shape')}}</th>
                        <th>{{__('Presentation')}}</th>
                        <th>{{__('PPV')}}</th>
                        <th>{{__('pH')}}</th>
                        <th>{{__('Price BR')}}</th>
                        <th>{{__('Princeps/Generique')}}</th>
                        <th>{{__('Refund Rate')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->number_of_medicine }}</td>
                            <td>{{ $medicine->description }}</td>
                            <td>{{ $medicine->works }}</td>
                            <td>{{ $medicine->prescription_required ? 'Yes' : 'No' }}</td>
                            <td>{{ $medicine->meta_info }}</td>
                            <td>{{ $medicine->code }}</td>
                            <td>{{ $medicine->dci1 }}</td>
                            <td>{{ $medicine->dosage1 }}</td>
                            <td>{{ $medicine->unit_dosage1 }}</td>
                            <td>{{ $medicine->shape }}</td>
                            <td>{{ $medicine->presentation }}</td>
                            <td>{{ $medicine->ppv }}</td>
                            <td>{{ $medicine->ph }}</td>
                            <td>{{ $medicine->price_br }}</td>
                            <td>{{ $medicine->princeps_generique }}</td>
                            <td>{{ $medicine->refund_rate }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination mt-4">
                {{ $medicines->links() }}
            </div>
        </div>
    </div>
</div>
</section>

@endsection
