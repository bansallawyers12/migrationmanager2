@extends('layouts.crm_client_detail_dashboard')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/client-forms.css')}}">
    <link rel="stylesheet" href="{{asset('css/clients/edit-client-components.css')}}">
    <link rel="stylesheet" href="{{asset('css/leads/lead-form.css')}}">
    
    <style>
        /* Compact Error Display Styles */
        .form-validation-errors {
            margin: 20px 0;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .error-container {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .error-container h4 {
            color: #721c24;
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .error-container ul {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
        }
        
        .error-container li {
            color: #721c24;
            font-size: 13px;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .error-container li:last-child {
            margin-bottom: 0;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-validation-errors {
                margin: 15px 10px;
            }
            
            .error-container {
                padding: 12px 15px;
            }
            
            .error-container h4 {
                font-size: 13px;
            }
            
            .error-container li {
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="crm-container">
        <div class="main-content">


            <!-- Mobile Sidebar Toggle -->
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar Navigation -->
            <div class="sidebar-navigation" id="sidebarNav">
                <div class="nav-header">
                    <h3><i class="fas fa-user-plus"></i> Create New Lead</h3>
                </div>
                <nav class="nav-menu">
                    <button class="nav-item active" onclick="scrollToSection('personalSection')">
                        <i class="fas fa-user-circle"></i>
                        <span>Personal</span>
                    </button>
                </nav>
                
                <!-- Actions in Sidebar -->
                <div class="sidebar-actions">
                    <button class="nav-item back-btn" onclick="window.location.href='{{ route('dashboard') }}'">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="submit" form="createLeadForm" class="nav-item save-btn">
                        <i class="fas fa-save"></i>
                        <span>Save Lead</span>
                    </button>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content-area">
                
                {{-- Error Display Section --}}
                @if($errors->any())
                    <div class="alert alert-danger" style="margin: 20px 0; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px;">
                        <h4 style="margin: 0 0 10px 0; color: #721c24; font-size: 16px;">
                            <i class="fas fa-exclamation-triangle"></i> Please fix the following errors:
                        </h4>
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li style="color: #721c24; margin-bottom: 5px;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger" style="margin: 20px 0; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px;">
                        <h4 style="margin: 0; color: #721c24; font-size: 16px;">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </h4>
                    </div>
                @endif
                
                @if(session('success'))
                    <div class="alert alert-success" style="margin: 20px 0; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px;">
                        <h4 style="margin: 0; color: #155724; font-size: 16px;">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </h4>
                    </div>
                @endif
                
                <form id="createLeadForm" action="{{ route('leads.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf


                    {{-- ==================== PERSONAL SECTION ==================== --}}
                    <section id="personalSection" class="content-section">
                        <!-- Basic Information -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-user-circle"></i> Basic Information</h3>
                            </div>
                            
                            <div class="content-grid">
                                <div class="form-group">
                                    <label for="firstName">First Name <span class="text-danger">*</span></label>
                                    <input type="text" id="firstName" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="lastName">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" id="lastName" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="dob">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="text" id="dob" name="dob" value="{{ old('dob') }}" class="date-picker" placeholder="dd/mm/yyyy" required>
                                    @error('dob')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="text" id="age" name="age" value="{{ old('age') }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="maritalStatus">Marital Status</label>
                                    <select id="maritalStatus" name="marital_status">
                                        <option value="">Select Marital Status</option>
                                        <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Defacto" {{ (old('marital_status') == 'Defacto' || old('marital_status') == 'De Facto') ? 'selected' : '' }}>De Facto</option>
                                        <option value="Separated" {{ old('marital_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                    @error('marital_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </section>

                        <!-- Phone Numbers -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-phone"></i> Phone Number <span class="text-danger">*</span></h3>
                            </div>
                            
                            <div class="repeatable-section">
                                <div class="content-grid">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select name="contact_type_hidden[0]" class="contact-type-selector">
                                            <option value="Personal">Personal</option>
                                            <option value="Work">Work</option>
                                            <option value="Mobile">Mobile</option>
                                            <option value="Business">Business</option>
                                            <option value="Secondary">Secondary</option>
                                            <option value="Father">Father</option>
                                            <option value="Mother">Mother</option>
                                            <option value="Brother">Brother</option>
                                            <option value="Sister">Sister</option>
                                            <option value="Uncle">Uncle</option>
                                            <option value="Aunt">Aunt</option>
                                            <option value="Cousin">Cousin</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Others">Others</option>
                                            <option value="Not In Use">Not In Use</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Country Code</label>
                                        <select name="country_code[0]" class="country-code-selector">
                                            <option value="+61" selected>🇦🇺 +61</option>
                                            <option value="+91">🇮🇳 +91</option>
                                            <option value="+1">🇺🇸 +1</option>
                                            <option value="+44">🇬🇧 +44</option>
                                            <option value="+49">🇩🇪 +49</option>
                                            <option value="+33">🇫🇷 +33</option>
                                            <option value="+86">🇨🇳 +86</option>
                                            <option value="+81">🇯🇵 +81</option>
                                            <option value="+82">🇰🇷 +82</option>
                                            <option value="+65">🇸🇬 +65</option>
                                            <option value="+60">🇲🇾 +60</option>
                                            <option value="+66">🇹🇭 +66</option>
                                            <option value="+63">🇵🇭 +63</option>
                                            <option value="+84">🇻🇳 +84</option>
                                            <option value="+62">🇮🇩 +62</option>
                                            <option value="+39">🇮🇹 +39</option>
                                            <option value="+34">🇪🇸 +34</option>
                                            <option value="+7">🇷🇺 +7</option>
                                            <option value="+55">🇧🇷 +55</option>
                                            <option value="+52">🇲🇽 +52</option>
                                            <option value="+54">🇦🇷 +54</option>
                                            <option value="+56">🇨🇱 +56</option>
                                            <option value="+57">🇨🇴 +57</option>
                                            <option value="+51">🇵🇪 +51</option>
                                            <option value="+58">🇻🇪 +58</option>
                                            <option value="+27">🇿🇦 +27</option>
                                            <option value="+20">🇪🇬 +20</option>
                                            <option value="+234">🇳🇬 +234</option>
                                            <option value="+254">🇰🇪 +254</option>
                                            <option value="+233">🇬🇭 +233</option>
                                            <option value="+212">🇲🇦 +212</option>
                                            <option value="+213">🇩🇿 +213</option>
                                            <option value="+216">🇹🇳 +216</option>
                                            <option value="+218">🇱🇾 +218</option>
                                            <option value="+220">🇬🇲 +220</option>
                                            <option value="+221">🇸🇳 +221</option>
                                            <option value="+222">🇲🇷 +222</option>
                                            <option value="+223">🇲🇱 +223</option>
                                            <option value="+224">🇬🇳 +224</option>
                                            <option value="+225">🇨🇮 +225</option>
                                            <option value="+226">🇧🇫 +226</option>
                                            <option value="+227">🇳🇪 +227</option>
                                            <option value="+228">🇹🇬 +228</option>
                                            <option value="+229">🇧🇯 +229</option>
                                            <option value="+230">🇲🇺 +230</option>
                                            <option value="+231">🇱🇷 +231</option>
                                            <option value="+232">🇸🇱 +232</option>
                                            <option value="+235">🇹🇩 +235</option>
                                            <option value="+236">🇨🇫 +236</option>
                                            <option value="+237">🇨🇲 +237</option>
                                            <option value="+238">🇨🇻 +238</option>
                                            <option value="+239">🇸🇹 +239</option>
                                            <option value="+240">🇬🇶 +240</option>
                                            <option value="+241">🇬🇦 +241</option>
                                            <option value="+242">🇨🇬 +242</option>
                                            <option value="+243">🇨🇩 +243</option>
                                            <option value="+244">🇦🇴 +244</option>
                                            <option value="+245">🇬🇼 +245</option>
                                            <option value="+246">🇮🇴 +246</option>
                                            <option value="+247">🇦🇨 +247</option>
                                            <option value="+248">🇸🇨 +248</option>
                                            <option value="+249">🇸🇩 +249</option>
                                            <option value="+250">🇷🇼 +250</option>
                                            <option value="+251">🇪🇹 +251</option>
                                            <option value="+252">🇸🇴 +252</option>
                                            <option value="+253">🇩🇯 +253</option>
                                            <option value="+255">🇹🇿 +255</option>
                                            <option value="+256">🇺🇬 +256</option>
                                            <option value="+257">🇧🇮 +257</option>
                                            <option value="+258">🇲🇿 +258</option>
                                            <option value="+260">🇿🇲 +260</option>
                                            <option value="+261">🇲🇬 +261</option>
                                            <option value="+262">🇷🇪 +262</option>
                                            <option value="+263">🇿🇼 +263</option>
                                            <option value="+264">🇳🇦 +264</option>
                                            <option value="+265">🇲🇼 +265</option>
                                            <option value="+266">🇱🇸 +266</option>
                                            <option value="+267">🇧🇼 +267</option>
                                            <option value="+268">🇸🇿 +268</option>
                                            <option value="+269">🇰🇲 +269</option>
                                            <option value="+290">🇸🇭 +290</option>
                                            <option value="+291">🇪🇷 +291</option>
                                            <option value="+297">🇦🇼 +297</option>
                                            <option value="+298">🇫🇴 +298</option>
                                            <option value="+299">🇬🇱 +299</option>
                                            <option value="+30">🇬🇷 +30</option>
                                            <option value="+31">🇳🇱 +31</option>
                                            <option value="+32">🇧🇪 +32</option>
                                            <option value="+351">🇵🇹 +351</option>
                                            <option value="+352">🇱🇺 +352</option>
                                            <option value="+353">🇮🇪 +353</option>
                                            <option value="+354">🇮🇸 +354</option>
                                            <option value="+355">🇦🇱 +355</option>
                                            <option value="+356">🇲🇹 +356</option>
                                            <option value="+357">🇨🇾 +357</option>
                                            <option value="+358">🇫🇮 +358</option>
                                            <option value="+359">🇧🇬 +359</option>
                                            <option value="+36">🇭🇺 +36</option>
                                            <option value="+370">🇱🇹 +370</option>
                                            <option value="+371">🇱🇻 +371</option>
                                            <option value="+372">🇪🇪 +372</option>
                                            <option value="+373">🇲🇩 +373</option>
                                            <option value="+374">🇦🇲 +374</option>
                                            <option value="+375">🇧🇾 +375</option>
                                            <option value="+376">🇦🇩 +376</option>
                                            <option value="+377">🇲🇨 +377</option>
                                            <option value="+378">🇸🇲 +378</option>
                                            <option value="+380">🇺🇦 +380</option>
                                            <option value="+381">🇷🇸 +381</option>
                                            <option value="+382">🇲🇪 +382</option>
                                            <option value="+383">🇽🇰 +383</option>
                                            <option value="+385">🇭🇷 +385</option>
                                            <option value="+386">🇸🇮 +386</option>
                                            <option value="+387">🇧🇦 +387</option>
                                            <option value="+389">🇲🇰 +389</option>
                                            <option value="+40">🇷🇴 +40</option>
                                            <option value="+41">🇨🇭 +41</option>
                                            <option value="+42">🇨🇿 +42</option>
                                            <option value="+43">🇦🇹 +43</option>
                                            <option value="+45">🇩🇰 +45</option>
                                            <option value="+46">🇸🇪 +46</option>
                                            <option value="+47">🇳🇴 +47</option>
                                            <option value="+48">🇵🇱 +48</option>
                                            <option value="+90">🇹🇷 +90</option>
                                            <option value="+92">🇵🇰 +92</option>
                                            <option value="+93">🇦🇫 +93</option>
                                            <option value="+94">🇱🇰 +94</option>
                                            <option value="+95">🇲🇲 +95</option>
                                            <option value="+960">🇲🇻 +960</option>
                                            <option value="+961">🇱🇧 +961</option>
                                            <option value="+962">🇯🇴 +962</option>
                                            <option value="+963">🇸🇾 +963</option>
                                            <option value="+964">🇮🇶 +964</option>
                                            <option value="+965">🇰🇼 +965</option>
                                            <option value="+966">🇸🇦 +966</option>
                                            <option value="+967">🇾🇪 +967</option>
                                            <option value="+968">🇴🇲 +968</option>
                                            <option value="+970">🇵🇸 +970</option>
                                            <option value="+971">🇦🇪 +971</option>
                                            <option value="+972">🇮🇱 +972</option>
                                            <option value="+973">🇧🇭 +973</option>
                                            <option value="+974">🇶🇦 +974</option>
                                            <option value="+975">🇧🇹 +975</option>
                                            <option value="+976">🇲🇳 +976</option>
                                            <option value="+977">🇳🇵 +977</option>
                                            <option value="+992">🇹🇯 +992</option>
                                            <option value="+993">🇹🇲 +993</option>
                                            <option value="+994">🇦🇿 +994</option>
                                            <option value="+995">🇬🇪 +995</option>
                                            <option value="+996">🇰🇬 +996</option>
                                            <option value="+998">🇺🇿 +998</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="phone[0]" class="form-control" placeholder="Enter phone number" value="{{ old('phone.0') }}" required>
                                        @error('phone.0')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                        </section>

                        <!-- Email Addresses -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-envelope"></i> Email Address <span class="text-danger">*</span></h3>
                            </div>
                            
                            <div class="repeatable-section">
                            <div class="content-grid">
                                <div class="form-group">
                                        <label>Type</label>
                                        <select name="email_type_hidden[0]" class="email-type-selector">
                                            <option value="Personal">Personal</option>
                                            <option value="Work">Work</option>
                                            <option value="Business">Business</option>
                                            <option value="Secondary">Secondary</option>
                                            <option value="Father">Father</option>
                                            <option value="Mother">Mother</option>
                                            <option value="Brother">Brother</option>
                                            <option value="Sister">Sister</option>
                                            <option value="Uncle">Uncle</option>
                                            <option value="Aunt">Aunt</option>
                                            <option value="Cousin">Cousin</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Others">Others</option>
                                            <option value="Not In Use">Not In Use</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                        <label>Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email[0]" class="form-control" placeholder="Enter email address" value="{{ old('email.0') }}" required>
                                        @error('email.0')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                </div>
                                </div>
                            </div>
                            
                        </section>
                    </section>

                    <!-- Form Actions (Hidden for floating button) -->
                    <div class="form-actions" style="margin-top: 30px; padding: 20px; background: white; border-radius: 8px; display: flex; gap: 15px; justify-content: flex-end; visibility: hidden;">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="hiddenSubmitBtn">
                            <i class="fas fa-save"></i> Save Lead
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>

    <!-- Floating Save Button -->
    <div class="floating-save-container">
        <div class="floating-save-buttons">
            <button type="button" class="btn btn-floating btn-cancel" onclick="window.history.back()">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </button>
            <button type="button" class="btn btn-floating btn-save" id="floatingSaveBtn">
                <i class="fas fa-save"></i>
                <span>Save Lead</span>
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/leads/lead-form-navigation.js') }}"></script>
    <script src="{{ asset('js/leads/lead-form.js') }}"></script>
    
    <!-- Ensure Daterangepicker is loaded -->
    <script>
        // Fallback: Load daterangepicker if not already loaded
        if (typeof $.fn.daterangepicker === 'undefined') {
            console.log('Loading Daterangepicker...');
            $.getScript('{{ asset("js/daterangepicker.js") }}', function() {
                console.log('✅ Daterangepicker loaded via fallback');
                // Initialize after loading
                setTimeout(initDatePicker, 100);
            }).fail(function() {
                console.error('❌ Failed to load Daterangepicker');
            });
        }
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            
            const floatingSaveBtn = document.getElementById('floatingSaveBtn');
            const hiddenSubmitBtn = document.getElementById('hiddenSubmitBtn');
            const form = document.getElementById('createLeadForm');
            const floatingContainer = document.querySelector('.floating-save-container');
            
            console.log('Elements found:', {
                floatingSaveBtn: !!floatingSaveBtn,
                hiddenSubmitBtn: !!hiddenSubmitBtn,
                form: !!form,
                floatingContainer: !!floatingContainer
            });
            
            // Add form submit event listener for debugging
            form.addEventListener('submit', function(e) {
                console.log('Form submit event triggered');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                
                // Check CSRF token
                const csrfToken = document.querySelector('input[name="_token"]');
                console.log('CSRF token found:', !!csrfToken);
                if (csrfToken) {
                    console.log('CSRF token value:', csrfToken.value);
                }
            });
            
            // Add invalid event listener to show validation errors clearly
            form.addEventListener('invalid', function(e) {
                console.log('Form validation failed on field:', e.target.name);
                
                // Scroll to the first invalid field
                e.target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Highlight the invalid field
                e.target.focus();
            }, true);
            
            // Handle floating save button click
            floatingSaveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Floating save button clicked');
                console.log('Form element:', form);
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                
                // Check form data
                const formData = new FormData(form);
                console.log('Form data entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }
                
                // Use requestSubmit() to trigger HTML5 validation and show error messages
                console.log('Submitting form with validation...');
                try {
                    // Try modern requestSubmit (triggers validation)
                    if (form.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        // Fallback: trigger click on hidden submit button
                        hiddenSubmitBtn.click();
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    // Last resort fallback
                    hiddenSubmitBtn.click();
                }
            });
            
            // Add scroll-based visibility control
            let lastScrollTop = 0;
            let ticking = false;
            
            function updateFloatingButton() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;
                
                // Show button when not at the very top or bottom
                if (scrollTop > 100 && scrollTop < documentHeight - windowHeight - 100) {
                    floatingContainer.classList.remove('hidden');
                    floatingContainer.classList.add('visible');
                } else if (scrollTop <= 100) {
                    floatingContainer.classList.add('hidden');
                    floatingContainer.classList.remove('visible');
                }
                
                lastScrollTop = scrollTop;
                ticking = false;
            }
            
            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateFloatingButton);
                    ticking = true;
                }
            }
            
            window.addEventListener('scroll', requestTick);
            
            // Initialize button state
            updateFloatingButton();
            
            // Add keyboard shortcut for save (Ctrl+S or Cmd+S)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    console.log('Keyboard shortcut (Ctrl/Cmd+S) triggered');
                    // Trigger the floating save button click (which now properly validates)
                    floatingSaveBtn.click();
                }
            });
            
            // Add visual feedback for form changes
            const formInputs = form.querySelectorAll('input, select, textarea');
            let formChanged = false;
            
            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                    updateSaveButtonState();
                });
                
                input.addEventListener('input', function() {
                    formChanged = true;
                    updateSaveButtonState();
                });
            });
            
            function updateSaveButtonState() {
                if (formChanged) {
                    floatingSaveBtn.style.background = 'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)';
                    floatingSaveBtn.querySelector('span').textContent = 'Save Changes';
                } else {
                    floatingSaveBtn.style.background = 'linear-gradient(135deg, #6777ef 0%, #47c363 100%)';
                    floatingSaveBtn.querySelector('span').textContent = 'Save Lead';
                }
            }
        });
    </script>
    
    <script>
    // Initialize form with at least one field in each required sections
    document.addEventListener('DOMContentLoaded', function() {
        // Add initial phone and email fields
        // Phone and email fields are now static HTML, no need to initialize dynamically
        
        // Display validation errors for phone and email fields
        displayFieldErrors();
        
        // Add real-time error clearing for phone and email fields
        setupErrorClearing();
        
        // DOB to Age calculation (same as client edit page)
        const dobField = document.getElementById('dob');
        const ageField = document.getElementById('age');
        if (dobField && ageField) {
            // Initialize age if DOB exists
            if (dobField.value) {
                ageField.value = calculateAge(dobField.value);
            }

            // Handle manual input changes (e.g., typing or pasting)
            dobField.addEventListener('input', function() {
                ageField.value = calculateAge(this.value);
            });
        }
    });
    
    // Initialize datepicker after all scripts are loaded
    $(document).ready(function() {
        // Wait a bit for all scripts to load
        setTimeout(function() {
            initDatePicker();
        }, 500);
    });
    
    // Function to display validation errors for each field
    function displayFieldErrors() {
        // Get all error messages from Laravel
        const errors = @json($errors->all());
        const errorBag = @json($errors->getMessageBag()->toArray());
        
        // Clear any existing error messages first
        document.querySelectorAll('.phone-error, .email-error, .field-error').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        
        // Check if we have field-specific errors
        let hasFieldSpecificErrors = false;
        
        // Display phone errors
        Object.keys(errorBag).forEach(key => {
            if (key.startsWith('phone.')) {
                hasFieldSpecificErrors = true;
                const index = key.split('.')[1];
                const errorElement = document.querySelector(`.phone-error-${index}`);
                if (errorElement) {
                    errorElement.textContent = errorBag[key][0];
                    errorElement.style.display = 'block';
                    errorElement.style.color = '#dc3545';
                    errorElement.style.fontSize = '12px';
                    errorElement.style.marginTop = '5px';
                }
            } else if (key === 'phone') {
                // General phone error - show in the section
                const phoneContainer = document.getElementById('phoneNumbersContainer');
                if (phoneContainer) {
                    // Remove existing general error
                    const existingError = phoneContainer.querySelector('.general-phone-error');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'general-phone-error text-danger';
                    errorDiv.style.marginTop = '10px';
                    errorDiv.style.fontSize = '12px';
                    errorDiv.textContent = errorBag[key][0];
                    phoneContainer.appendChild(errorDiv);
                }
            }
        });
        
        // Display email errors
        Object.keys(errorBag).forEach(key => {
            if (key.startsWith('email.')) {
                hasFieldSpecificErrors = true;
                const index = key.split('.')[1];
                const errorElement = document.querySelector(`.email-error-${index}`);
                if (errorElement) {
                    errorElement.textContent = errorBag[key][0];
                    errorElement.style.display = 'block';
                    errorElement.style.color = '#dc3545';
                    errorElement.style.fontSize = '12px';
                    errorElement.style.marginTop = '5px';
                }
            } else if (key === 'email') {
                // General email error - show in the section
                const emailContainer = document.getElementById('emailAddressesContainer');
                if (emailContainer) {
                    // Remove existing general error
                    const existingError = emailContainer.querySelector('.general-email-error');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'general-email-error text-danger';
                    errorDiv.style.marginTop = '10px';
                    errorDiv.style.fontSize = '12px';
                    errorDiv.textContent = errorBag[key][0];
                    emailContainer.appendChild(errorDiv);
                }
            }
        });
        
        // Hide general error container if we have field-specific errors
        const generalErrorContainer = document.querySelector('.form-validation-errors');
        if (generalErrorContainer) {
            if (hasFieldSpecificErrors) {
                generalErrorContainer.style.display = 'none';
            } else {
                generalErrorContainer.style.display = 'block';
            }
        }
    }
    
    // Function to setup error clearing when user types
    function setupErrorClearing() {
        // Clear phone errors when user types
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('phone-number-input')) {
                const index = e.target.name.match(/\[(\d+)\]/)[1];
                const errorElement = document.querySelector(`.phone-error-${index}`);
                if (errorElement) {
                    errorElement.style.display = 'none';
                    errorElement.textContent = '';
                }
            }
            
            if (e.target.classList.contains('email-input')) {
                const index = e.target.name.match(/\[(\d+)\]/)[1];
                const errorElement = document.querySelector(`.email-error-${index}`);
                if (errorElement) {
                    errorElement.style.display = 'none';
                    errorElement.textContent = '';
                }
            }
        });
    }
    
    // Function to initialize daterangepicker (same as client edit page)
    function initDatePicker() {
        try {
            // Check if jQuery and daterangepicker are available
            if (typeof $ !== 'undefined' && typeof $.fn.daterangepicker !== 'undefined') {
                const dobInput = document.getElementById('dob');
                const ageInput = document.getElementById('age');
                
                if (dobInput && ageInput) {
                    // Initialize daterangepicker (same as client edit page)
                    $(dobInput).daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        locale: {
                            format: 'DD/MM/YYYY',
                            applyLabel: 'Apply',
                            cancelLabel: 'Cancel',
                            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                            monthNames: [
                                'January', 'February', 'March', 'April', 'May', 'June',
                                'July', 'August', 'September', 'October', 'November', 'December'
                            ],
                            firstDay: 1
                        },
                        autoApply: true,
                        minDate: '01/01/1000',
                        minYear: 1000,
                        maxYear: parseInt(moment().format('YYYY')) + 50
                    }).on('apply.daterangepicker', function(ev, picker) {
                        // Update age when date is selected (same as client edit page)
                        const dobValue = dobInput.value;
                        ageInput.value = calculateAge(dobValue);
                    });
                    
                    console.log('✅ DOB Daterangepicker initialized successfully');
                }
            } else {
                console.warn('⚠️ jQuery or Daterangepicker not available');
                console.log('jQuery available:', typeof $ !== 'undefined');
                console.log('Daterangepicker available:', typeof $.fn.daterangepicker !== 'undefined');
            }
        } catch(e) {
            console.error('❌ Daterangepicker initialization failed:', e.message);
        }
    }
    
    // Age calculation function (same as client edit page)
    function calculateAge(dob) {
        if (!dob || !/^\d{2}\/\d{2}\/\d{4}$/.test(dob)) return '';

        try {
            const [day, month, year] = dob.split('/').map(Number);
            const dobDate = new Date(year, month - 1, day);
            if (isNaN(dobDate.getTime())) return ''; // Invalid date

            const today = new Date();
            let years = today.getFullYear() - dobDate.getFullYear();
            let months = today.getMonth() - dobDate.getMonth();

            if (months < 0) {
                years--;
                months += 12;
            }

            if (today.getDate() < dobDate.getDate()) {
                months--;
                if (months < 0) {
                    years--;
                    months += 12;
                }
            }

            return years + ' years ' + months + ' months';
        } catch (e) {
            return '';
        }
    }
    
    </script>
@endpush

