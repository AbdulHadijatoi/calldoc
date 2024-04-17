@extends('layout.mainlayout_admin',['activePage' => 'appointment'])

@section('title',__('Prescription'))
@section('content')

<section class="section">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title mb-0">{{__('Add Prescription')}}</h4>
            <div class="col-md-4 col-sm-6">
                <button type="button" class="btn btn-primary btn-block py-2" style="border-radius: 5px; border-style: none; text-transform: uppercase; font-weight: bold" id="submitBtn"><i class="fa fa-save"></i> {{__('Save')}}</button>
            </div>
        </div>
        <div class="card-body">
            <div id="originalFields" class="mb-4">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="medicine">{{__('Medicine Name')}}</label>
                        <div class="input-group" id="medicineSelect">
                            <select id="medicine" class="form-control select2">
                                @foreach ($medicines as $medicine)
                                <option value="{{ $medicine->name ? $medicine->name.', ' : '' }} {{ $medicine->dosage1 ? $medicine->dosage1.' ' : '' }} {{ $medicine->unit_dosage1 ? $medicine->unit_dosage1.', ' : '' }} {{ $medicine->shape ? $medicine->shape.', ' : '' }} {{ $medicine->presentation }}">{{ $medicine->name?$medicine->name.', ':'' }} {{ $medicine->dosage1?$medicine->dosage1.' ':'' }} {{ $medicine->unit_dosage1?$medicine->unit_dosage1.', ' :'' }} {{ $medicine->shape?$medicine->shape.', ':'' }} {{ $medicine->presentation }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group" id="medicineText" style="display: none;">    
                            <input type="text" id="medicine2" class="form-control" placeholder="Write your medicine here">
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="button" id="toggleMedicineBtn">{{ __('Want to write it yourself?') }}</button>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="day">{{__('Days')}}</label>
                        <input id="day" class="form-control" min="1" value="1" type="number">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="quantity">{{__('Quantity/Morning')}}</label>
                        <input id="quantity1" class="form-control" min="0" value="0" type="number">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="quantity">{{__('Quantity/Afternoon')}}</label>
                        <input id="quantity2" class="form-control" min="0" value="0" type="number">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="quantity">{{__('Quantity/Night')}}</label>
                        <input id="quantity3" class="form-control" min="0" value="0" type="number">
                    </div>
                    
                    <div class="form-group col-md-8">
                        <label for="remarks">{{__('Remarks')}}</label>
                        <input id="remarks" class="form-control" placeholder="Add remarks if needed" type="text">
                    </div>
                    
                    <div class="col-md-4 mt-sm-0 mt-md-4 pt-1">
                        <button type="button" class="btn btn-outline-info btn-block py-2" id="addBtn"><i class="fa fa-plus"></i> {{__('Add More Medicines')}}</button>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Record voice note for patient (OPTIONAL)</label>
                    <br>
                    <audio id="recorder" muted hidden></audio>

                    <input type="hidden" id="appointment_id" value="{{ $appointment->id }}">
                    <button class="btn btn-primary py-3 col-md-3" id="start">
                        <i class="fa fa-microphone"></i> Start Recording
                    </button>
                    <button disabled class="btn btn-info py-3 col-md-3 mt-md-0 mt-sm-1" id="stop">
                        <i class="fa fa-stop"></i> Stop
                    </button>
                    <button disabled class="btn btn-success py-3 col-md-3 mt-md-0 mt-sm-1" id="saveRecording">
                        <i class="fa fa-save"></i> Save
                    </button>
                    <audio id="player" controls class="d-none"></audio>
                </div>
            </div>
            <!-- Form -->
            <form action="{{ url('addPrescription') }}" method="post" class="myform" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                <input type="hidden" name="user_id" value="{{ $appointment->user_id }}">
                <input type="hidden" name="pres_id" id="pres_id">
                
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-center">
                        <thead>
                            <tr>
                                <th>{{__('Medicine Name')}}</th>
                                <th>{{__('Days')}}</th>
                                <th>{{__('Quantity/Morning')}}</th>
                                <th>{{__('Quantity/Afternoon')}}</th>
                                <th>{{__('Quantity/Night')}}</th>
                                <th>{{__('Remarks')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                        <tbody class="tBody">
                            <!-- Table body will be filled dynamically -->
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

</section>


<script>
    // RECORDING SCRIPT:BEGINS
    class VoiceRecorder {
        constructor() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                console.log("getUserMedia supported")
            } else {
                console.log("getUserMedia is not supported on your browser!")
            }

            this.mediaRecorder
            this.stream
            this.chunks = []
            this.isRecording = false

            this.recorderRef = document.querySelector("#recorder")
            this.playerRef = document.querySelector("#player")
            this.startRef = document.querySelector("#start")
            this.stopRef = document.querySelector("#stop")
            this.saveRef = document.querySelector("#saveRecording")
            this.appointmentIdRef = document.querySelector("#appointment_id")
            this.presIdRef = document.querySelector("#pres_id")
            
            this.startRef.onclick = this.startRecording.bind(this)
            this.stopRef.onclick = this.stopRecording.bind(this)
            this.saveRef.onclick = this.saveRecording.bind(this)

            this.constraints = {
                audio: true,
                video: false
            }
            
        }

        handleSuccess(stream) {
            this.stream = stream
            this.stream.oninactive = () => {
                console.log("Stream ended!")
            };
            this.recorderRef.srcObject = this.stream
            this.mediaRecorder = new MediaRecorder(this.stream)
            console.log(this.mediaRecorder)
            this.mediaRecorder.ondataavailable = this.onMediaRecorderDataAvailable.bind(this)
            // this.mediaRecorder.onstop = this.onMediaRecorderStop.bind(this)
            this.recorderRef.play()
            this.mediaRecorder.start()
        }

        handleError(error) {
            console.log("navigator.getUserMedia error: ", error)
        }
        
        onMediaRecorderDataAvailable(e) { this.chunks.push(e.data) }
        
        saveRecording(){
            this.saveRef.disabled = true;
            this.stopRef.disabled = true;
            this.saveRef.innerHTML = 'Loading...';
            const blob = new Blob(this.chunks, { 'type': 'audio/ogg; codecs=opus' });
            this.chunks = [];
            this.stream.getAudioTracks().forEach(track => track.stop());
            this.stream = null;

            // Create a FormData object and append the audio Blob
            const formData = new FormData();
            formData.append('audio_clip', blob, 'recording.ogg');
            formData.append('appointment_id', this.appointmentIdRef.value);
            formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token

            // Send the audio data to the backend using fetch
            fetch('{{ url("save-audio-clip") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    this.saveRef.innerHTML = 'Done';
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            })
            .then(data => {
                // Handle the response from the backend if needed
                if(data.pres_id){
                    this.presIdRef.value = data.pres_id;
                }
                console.log(data);
            })
            .catch(error => {
                console.error('There was a problem with your fetch operation:', error);
            });
        }

        startRecording() {
            if (this.isRecording) return
            this.isRecording = true
            this.startRef.innerHTML = '<i style="font-size: 22px;" class="fa fa-microphone"></i>'
            this.stopRef.disabled = false;
            this.saveRef.innerHTML = '<i class="fa fa-save"></i> Save'
            this.playerRef.src = ''
            navigator.mediaDevices
                .getUserMedia(this.constraints)
                .then(this.handleSuccess.bind(this))
                .catch(this.handleError.bind(this))
        }
        
        stopRecording() {
            if (!this.isRecording) return
            this.isRecording = false
            this.startRef.innerHTML = '<i class="fa fa-microphone"></i> Start Recording'
            this.stopRef.innerHTML = 'Stop Recording'
            this.saveRef.disabled = false;
            this.recorderRef.pause()
            this.mediaRecorder.stop()
        }
        
    }

    window.voiceRecorder = new VoiceRecorder()

    // RECORDING SCRIPT:ENDS


    $(document).ready(function() {
        var btnToggle = $('#toggleMedicineBtn');
        var selectMedicineLabel = "{{ __('Select Medicine') }}";
        var medicineFieldRequiredLabel = "{{ __('Medicine field is required') }}";
        var write_yourself_label = "{{ __('Want to write it yourself?') }}";

        $("#addBtn").on('click', function () {
            var medicine = $('#medicine').val();
            var medicine2 = $('#medicine2').val();
            var day = $('#day').val();
            var quantity1 = $('#quantity1').val();
            var quantity2 = $('#quantity2').val();
            var quantity3 = $('#quantity3').val();
            var remarks = $('#remarks').val();
            var medicineText = medicine;
            if(btnToggle.html() == selectMedicineLabel){
                medicineText = medicine2
            }
            if(medicineText == '' || medicineText == null){
                alert(medicineFieldRequiredLabel);
                return;
            }
            // Add read-only row to table
            $('.tBody').append(
                '<tr>' +
                '<td><input type="text" class="form-control-plaintext" name="medicine[]" value="' + medicineText + '" readonly></td>' +
                '<td><input type="text" class="form-control-plaintext" name="day[]" value="' + day + '" readonly></td>' +
                '<td><input type="text" class="form-control-plaintext" name="qty_morning[]" value="' + quantity1 + '" readonly></td>' +
                '<td><input type="text" class="form-control-plaintext" name="qty_afternoon[]" value="' + quantity2 + '" readonly></td>' +
                '<td><input type="text" class="form-control-plaintext" name="qty_night[]" value="' + quantity3 + '" readonly></td>' +
                '<td><input type="text" class="form-control-plaintext" name="remarks[]" value="' + remarks + '" readonly></td>' +
                '<td><button type="button" class="btn btn-danger deleteBtn">{{__('Remove')}}</button></td>' +
                '</tr>'
            );

            // Clear original fields
            $('#medicine2').val('');
            $('#day').val('1');
            $('#quantity1').val('0');
            $('#quantity2').val('0');
            $('#quantity3').val('0');
            $('#remarks').val('');
        });

        $(document).on('click', '.deleteBtn', function() {
            $(this).closest('tr').remove();
        });

        function isTableBodyEmpty() {
            return $('.tBody').find('tr').length === 0;
        }
        
        $("#submitBtn").on('click', function () {
            if (isTableBodyEmpty()) {
                alert('Please add at least one medicine before submitting.');
                return false; // Prevent form submission
            }
            $(".myform").submit();
        });

        $('#toggleMedicineBtn').on('click', function() {
            var selectOption = $('#medicineSelect').is(':visible');
            $('#medicineSelect').hide();
            if (selectOption) {
                btnToggle.html(selectMedicineLabel);
                $('#medicineSelect').hide();
                $('#medicineText').show();
            } else {
                btnToggle.html(write_yourself_label);
                $('#medicineSelect').show();
                $('#medicineText').hide();
            }
        });

        
    });
</script>

@endsection
