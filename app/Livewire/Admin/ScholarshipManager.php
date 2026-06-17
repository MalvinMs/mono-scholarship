<?php

namespace App\Livewire\Admin;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ScholarshipManager extends Component
{
    use WithPagination;

    public $name, $slug, $predecessor_scholarship_id;
    public $description, $academic_year, $fund_amount;
    public $quota_primary, $quota_reserve = 0;
    public $date_start, $date_end, $status = 'draft';
    public $scholarshipId;
    public $isEditing = false;
    public $showForm = false;

    public function render()
    {
        return view('livewire.admin.scholarship-manager', [
            'scholarships' => Scholarship::with('predecessor')->latest()->paginate(10),
            'existingScholarships' => Scholarship::orderBy('academic_year', 'desc')->get(),
        ])->layout('components.layouts.app', ['title' => 'Program Beasiswa']);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        $this->scholarshipId = $scholarship->id;
        $this->name = $scholarship->name;
        $this->description = $scholarship->description;
        $this->academic_year = $scholarship->academic_year;
        $this->fund_amount = $scholarship->fund_amount;
        $this->quota_primary = $scholarship->quota_primary;
        $this->quota_reserve = $scholarship->quota_reserve;
        $this->date_start = $scholarship->date_start?->format('Y-m-d');
        $this->date_end = $scholarship->date_end?->format('Y-m-d');
        $this->status = $scholarship->status;
        $this->predecessor_scholarship_id = $scholarship->predecessor_scholarship_id;
        $this->showForm = true;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->slug = Str::slug($this->name);

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', Rule::unique('scholarships', 'slug')->ignore($this->scholarshipId)],
            'description' => 'nullable|string',
            'academic_year' => 'required|string|max:20',
            'fund_amount' => 'nullable|integer|min:0',
            'quota_primary' => 'required|integer|min:1',
            'quota_reserve' => 'nullable|integer|min:0',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'status' => 'required|in:draft,open,renewal_open,renewal_closed,closed,selecting,announced',
            'predecessor_scholarship_id' => 'nullable|exists:scholarships,id',
        ]);

        $data = array_merge($validated, [
            'created_by' => auth()->id(),
        ]);

        if ($this->isEditing) {
            Scholarship::find($this->scholarshipId)->update($data);
            session()->flash('message', 'Program beasiswa berhasil diperbarui.');
        } else {
            Scholarship::create($data);
            session()->flash('message', 'Program beasiswa berhasil dibuat.');
        }

        $this->resetForm();
    }

    public function delete($id)
    {
        Scholarship::findOrFail($id)->delete();
        session()->flash('message', 'Program beasiswa berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->reset(['name', 'slug', 'description', 'academic_year', 'fund_amount',
            'quota_primary', 'quota_reserve', 'date_start', 'date_end', 'status',
            'predecessor_scholarship_id', 'scholarshipId', 'showForm', 'isEditing']);
        $this->quota_reserve = 0;
        $this->status = 'draft';
    }
}
