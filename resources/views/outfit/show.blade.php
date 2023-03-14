@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $outfit->type }} Master: {{ $outfit->getMaster->name }}
                        {{ $outfit->getMaster->surname }}</div>
                    <div class="card-body">
                        <div class="outfit-container">
                            <div class="outfit-container__size">
                                {{ $outfit->type }} Size: {{ $outfit->size }}
                            </div>
                            <div class="outfit-container__color" style="background:{{ $outfit->color }};">
                                {{ $outfit->color }}
                            </div>
                        </div>
                        <div class="outfit-container__about">
                            {!! $outfit->about !!}
                        </div>
                        <a href="{{ route('outfit.edit', $outfit) }}" class="btn btn-info mt-2">Edit</a>
                        <a href="{{ route('outfit.pdf', $outfit) }}" class="btn btn-info mt-2">PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('title')
    {{ $outfit->type }}
@endsection
