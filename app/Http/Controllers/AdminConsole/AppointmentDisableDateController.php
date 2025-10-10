<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\BookService;
use App\Models\BookServiceDisableSlot;

use Auth;
use Config;

class AppointmentDisableDateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display block slots grouped by person like the old system.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		// Get all disable slots with their related slot per person data
        $disableSlots = BookServiceDisableSlot::with('slotPerPerson')
            ->orderBy('disabledates', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Group by person name for display like the old system
        $groupedSlots = [];
        foreach ($disableSlots as $slot) {
            $personName = $this->getPersonName($slot->slotPerPerson->person_id ?? 0);
            
            if (!isset($groupedSlots[$personName])) {
                $groupedSlots[$personName] = [];
            }
            
            $groupedSlots[$personName][] = [
                'id' => $slot->id,
                'date' => date('d/m/Y', strtotime($slot->disabledates)),
                'slots' => $slot->block_all == 1 ? 'Full Day Blocked' : $slot->slots,
                'block_all' => $slot->block_all,
                'slot_per_person_id' => $slot->book_service_slot_per_person_id
            ];
        }

        // Sort by person name
        ksort($groupedSlots);

        $totalData = $disableSlots->count();
        
        return view('AdminConsole.features.appointmentdisabledate.index', compact(['groupedSlots', 'totalData']));
    }

	public function create(Request $request)
	{
		// Get existing slot configurations for person selection
        $slotConfigs = \App\Models\BookServiceSlotPerPerson::with('bookService')
            ->whereHas('bookService', function($query) {
                $query->where('status', 1);
            })
            ->get();

        return view('AdminConsole.features.appointmentdisabledate.create', compact('slotConfigs'));
	}

	public function store(Request $request)
	{
		if ($request->isMethod('post'))
		{
			$requestData = $request->all();
			
			$this->validate($request, [
				'slot_per_person_id' => 'required|exists:book_service_slot_per_persons,id',
				'block_date' => 'required|date|after_or_equal:today',
				'block_type' => 'required|in:time_slots,full_day',
				'time_slots' => 'required_if:block_type,time_slots'
			]);

			// Additional validation for time slots format
			if ($requestData['block_type'] == 'time_slots') {
				$this->validateTimeSlotsFormat($requestData['time_slots']);
			}

			// Convert date format from dd/mm/yyyy to yyyy-mm-dd
			$blockDate = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['block_date'])->format('Y-m-d');

			$obj = new BookServiceDisableSlot;
			$obj->book_service_slot_per_person_id = $requestData['slot_per_person_id'];
			$obj->disabledates = $blockDate;
			$obj->block_all = ($requestData['block_type'] == 'full_day') ? 1 : 0;
			
			if ($requestData['block_type'] == 'time_slots') {
				$obj->slots = $requestData['time_slots'];
			} else {
				$obj->slots = null;
			}

			$saved = $obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('success', 'Block Slot Added Successfully');
			}
		}

		return $this->create($request);
	}

	public function edit(Request $request, $id = NULL)
	{
        if ($request->isMethod('post')) {
			$requestData = $request->all();
			
			$this->validate($request, [
				'id' => 'required|exists:book_service_disable_slots,id',
				'block_date' => 'required|date',
				'block_type' => 'required|in:time_slots,full_day',
				'time_slots' => 'required_if:block_type,time_slots'
			]);

			// Additional validation for time slots format
			if ($requestData['block_type'] == 'time_slots') {
				$this->validateTimeSlotsFormat($requestData['time_slots']);
			}
			$id = $this->decodeString($requestData['id']);

			$obj = BookServiceDisableSlot::find($id);
			if (!$obj) {
				return redirect()->back()->with('error', 'Block Slot Not Found');
			}

			// Convert date format from dd/mm/yyyy to yyyy-mm-dd
			$blockDate = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['block_date'])->format('Y-m-d');

			$obj->disabledates = $blockDate;
			$obj->block_all = ($requestData['block_type'] == 'full_day') ? 1 : 0;
			
			if ($requestData['block_type'] == 'time_slots') {
				$obj->slots = $requestData['time_slots'];
			} else {
				$obj->slots = null;
			}

			$saved = $obj->save();
            if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
				return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('success', 'Block Slot Updated Successfully');
			}
		} else {
			if(isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
				if(BookServiceDisableSlot::where('id', '=', $id)->exists()) {
					$fetchedData = BookServiceDisableSlot::with('slotPerPerson')->find($id);

                    return view('AdminConsole.features.appointmentdisabledate.edit', compact(['fetchedData']));
				} else {
					return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('error', 'Block Slot Not Found');
				}
			} else {
				return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('error', Config::get('constants.unauthorized'));
			}
		}
    }

    /**
     * Delete a block slot
     */
    public function destroy(Request $request, $id = NULL)
    {
        if(isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            if(BookServiceDisableSlot::where('id', '=', $id)->exists()) {
                $deleted = BookServiceDisableSlot::find($id)->delete();
                if($deleted) {
                    return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('success', 'Block Slot Deleted Successfully');
                } else {
                    return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('error', 'Error deleting block slot');
                }
            } else {
                return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('error', 'Block Slot Not Found');
            }
        } else {
            return Redirect::to('/adminconsole/features/appointment-dates-disable')->with('error', Config::get('constants.unauthorized'));
        }
    }

    /**
     * Get person name by ID
     */
    private function getPersonName($personId)
    {
        $personNames = [
            1 => 'Arun',
            2 => 'Shubam', 
            3 => 'Tourist',
            4 => 'Education',
            5 => 'Adelaide'
        ];
        
        return $personNames[$personId] ?? "User{$personId}";
    }

    /**
     * Validate time slots format - supports both ranges and individual slots
     */
    private function validateTimeSlotsFormat($timeSlots)
    {
        if (empty($timeSlots)) {
            return;
        }

        // Split by comma to get individual entries
        $entries = array_map('trim', explode(',', $timeSlots));
        
        foreach ($entries as $entry) {
            if (empty($entry)) {
                continue;
            }
            
            // Check if it's a range (contains " - ")
            if (strpos($entry, ' - ') !== false) {
                $range = explode(' - ', $entry);
                if (count($range) != 2) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []), 
                        ['time_slots' => ['Invalid time range format. Use "start_time - end_time" (e.g., "11:00 AM - 5:00 PM")']]
                    );
                }
                
                $startTime = trim($range[0]);
                $endTime = trim($range[1]);
                
                // Validate individual time formats
                if (!$this->isValidTimeFormat($startTime) || !$this->isValidTimeFormat($endTime)) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []), 
                        ['time_slots' => ['Invalid time format in range. Use format like "11:00 AM" or "5:00 PM"']]
                    );
                }
            } else {
                // Single time slot
                if (!$this->isValidTimeFormat($entry)) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []), 
                        ['time_slots' => ['Invalid time format. Use format like "11:00 AM" or "5:00 PM"']]
                    );
                }
            }
        }
    }

    /**
     * Check if time format is valid (supports various formats)
     */
    private function isValidTimeFormat($time)
    {
        // Common time formats to support
        $formats = [
            'g:i A',    // 11:00 AM
            'G:i',      // 11:00
            'h:i A',    // 11:00 AM (12-hour with leading zero)
            'H:i',      // 11:00 (24-hour with leading zero)
        ];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $time);
            if ($date && $date->format($format) === $time) {
                return true;
            }
        }
        
        return false;
    }
}


