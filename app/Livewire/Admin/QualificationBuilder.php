<?php

namespace App\Livewire\Admin;

use App\Models\Qualification;
use App\Models\QualificationGroup;
use App\Models\QualificationOption;
use App\Models\QualificationRange;
use App\Models\Scholarship;
use Livewire\Component;

class QualificationBuilder extends Component
{
    public Scholarship $scholarship;
    public bool $isLocked = false;

    // Group form
    public string $groupName = '';
    public string $groupDescription = '';
    public ?int $editingGroupId = null;
    public bool $showGroupForm = false;

    // Qualification form
    public string $qualName = '';
    public string $qualType = 'single_choice';
    public string $qualDescription = '';
    public bool $isRequired = true;
    public bool $isFileUploadRequired = false;
    public string $fileUploadLabel = '';
    public ?int $editingQualId = null;
    public ?int $parentGroupId = null;
    public bool $showQualForm = false;

    // Option form
    public string $optionLabel = '';
    public int $optionValue = 0;
    public string $optionDescription = '';
    public ?int $editingOptionId = null;
    public ?int $optionQualId = null;

    // Range form
    public string $rangeMin = '';
    public string $rangeMax = '';
    public int $rangeValue = 0;
    public string $rangeLabel = '';
    public ?int $editingRangeId = null;
    public ?int $rangeQualId = null;

    public ?int $selectedQualId = null;

    public function mount(Scholarship $scholarship)
    {
        $this->scholarship = $scholarship->load([
            'qualificationGroups.qualifications.options',
            'qualificationGroups.qualifications.ranges',
            'qualifications.options',
            'qualifications.ranges',
        ]);

        $this->isLocked = $scholarship->applications()->where('status', '!=', 'draft')->exists();
    }

    public function render()
    {
        $groups = $this->scholarship->qualificationGroups()
            ->with(['qualifications.options', 'qualifications.ranges'])
            ->orderBy('sort_order')
            ->get();

        $ungrouped = $this->scholarship->qualifications()
            ->with(['options', 'ranges'])
            ->whereNull('qualification_group_id')
            ->orderBy('sort_order')
            ->get();

        $selectedQual = $this->selectedQualId
            ? Qualification::with(['options', 'ranges'])->find($this->selectedQualId)
            : null;

        return view('livewire.admin.qualification-builder', compact('groups', 'ungrouped', 'selectedQual'))
            ->layout('components.layouts.app', ['title' => 'Konfigurasi Kualifikasi — ' . $this->scholarship->name]);
    }

    // === Groups ===
    public function openGroupForm(?int $id = null): void
    {
        if ($this->isLocked) return;
        if ($id) {
            $group = QualificationGroup::findOrFail($id);
            $this->editingGroupId = $id;
            $this->groupName = $group->name;
            $this->groupDescription = $group->description ?? '';
        } else {
            $this->editingGroupId = null;
            $this->groupName = '';
            $this->groupDescription = '';
        }
        $this->showGroupForm = true;
    }

    public function saveGroup(): void
    {
        $this->validate(['groupName' => 'required|string|max:255']);
        $data = ['name' => $this->groupName, 'description' => $this->groupDescription];

        if ($this->editingGroupId) {
            QualificationGroup::findOrFail($this->editingGroupId)->update($data);
        } else {
            $this->scholarship->qualificationGroups()->create($data + ['sort_order' => 0]);
        }

        $this->showGroupForm = false;
        $this->editingGroupId = null;
    }

    public function deleteGroup(int $id): void
    {
        QualificationGroup::findOrFail($id)->delete();
    }

    // === Qualifications ===
    public function openQualForm(?int $id = null, ?int $groupId = null): void
    {
        if ($this->isLocked) return;
        if ($id) {
            $qual = Qualification::findOrFail($id);
            $this->editingQualId = $id;
            $this->qualName = $qual->name;
            $this->qualType = $qual->type;
            $this->qualDescription = $qual->description ?? '';
            $this->isRequired = $qual->is_required;
            $this->isFileUploadRequired = $qual->is_file_upload_required;
            $this->fileUploadLabel = $qual->file_upload_label ?? '';
        } else {
            $this->editingQualId = null;
            $this->qualName = '';
            $this->qualType = 'single_choice';
            $this->qualDescription = '';
            $this->isRequired = true;
            $this->isFileUploadRequired = false;
            $this->fileUploadLabel = '';
        }
        $this->parentGroupId = $groupId;
        $this->showQualForm = true;
    }

    public function saveQualification(): void
    {
        $this->validate(['qualName' => 'required|string|max:255', 'qualType' => 'required|in:single_choice,multi_choice,numeric_range,file_upload,text']);

        $data = [
            'name' => $this->qualName,
            'type' => $this->qualType,
            'description' => $this->qualDescription,
            'is_required' => $this->isRequired,
            'is_file_upload_required' => $this->isFileUploadRequired,
            'file_upload_label' => $this->fileUploadLabel,
            'qualification_group_id' => $this->parentGroupId,
        ];

        if ($this->editingQualId) {
            Qualification::findOrFail($this->editingQualId)->update($data);
        } else {
            $this->scholarship->qualifications()->create($data + ['sort_order' => 0]);
        }

        $this->showQualForm = false;
        $this->editingQualId = null;
        $this->parentGroupId = null;
    }

    public function selectQual(int $id): void
    {
        $this->selectedQualId = $id;
        $this->optionQualId = $id;
        $this->rangeQualId = $id;
    }

    public function deleteQual(int $id): void
    {
        Qualification::findOrFail($id)->delete();
        if ($this->selectedQualId === $id) $this->selectedQualId = null;
    }

    // === Options ===
    public function saveOption(): void
    {
        $this->validate([
            'optionLabel' => 'required|string|max:255',
            'optionValue' => 'required|integer|min:0',
            'optionQualId' => 'required|exists:qualifications,id',
        ]);

        $data = [
            'qualification_id' => $this->optionQualId,
            'label' => $this->optionLabel,
            'value' => $this->optionValue,
            'description' => $this->optionDescription,
        ];

        if ($this->editingOptionId) {
            QualificationOption::findOrFail($this->editingOptionId)->update($data);
        } else {
            QualificationOption::create($data + ['sort_order' => 0]);
        }

        $this->editingOptionId = null;
        $this->optionLabel = '';
        $this->optionValue = 0;
        $this->optionDescription = '';
        $this->selectedQualId = $this->optionQualId;
    }

    public function editOption(int $id): void
    {
        $option = QualificationOption::findOrFail($id);
        $this->editingOptionId = $id;
        $this->optionQualId = $option->qualification_id;
        $this->optionLabel = $option->label;
        $this->optionValue = $option->value;
        $this->optionDescription = $option->description ?? '';
    }

    public function deleteOption(int $id): void
    {
        $qualId = QualificationOption::findOrFail($id)->qualification_id;
        QualificationOption::where('id', $id)->delete();
        $this->selectedQualId = $qualId;
    }

    public function cancelEditOption(): void
    {
        $this->editingOptionId = null;
        $this->optionLabel = '';
        $this->optionValue = 0;
        $this->optionDescription = '';
    }

    // === Ranges ===
    public function saveRange(): void
    {
        $this->validate([
            'rangeMin' => 'required|numeric',
            'rangeMax' => 'required|numeric|gte:range_min',
            'rangeValue' => 'required|integer|min:0',
            'rangeQualId' => 'required|exists:qualifications,id',
        ]);

        $data = [
            'qualification_id' => $this->rangeQualId,
            'range_min' => $this->rangeMin,
            'range_max' => $this->rangeMax,
            'value' => $this->rangeValue,
            'label' => $this->rangeLabel,
        ];

        if ($this->editingRangeId) {
            QualificationRange::findOrFail($this->editingRangeId)->update($data);
        } else {
            QualificationRange::create($data + ['sort_order' => 0]);
        }

        $this->editingRangeId = null;
        $this->rangeMin = '';
        $this->rangeMax = '';
        $this->rangeValue = 0;
        $this->rangeLabel = '';
        $this->selectedQualId = $this->rangeQualId;
    }

    public function editRange(int $id): void
    {
        $range = QualificationRange::findOrFail($id);
        $this->editingRangeId = $id;
        $this->rangeQualId = $range->qualification_id;
        $this->rangeMin = (string) $range->range_min;
        $this->rangeMax = (string) $range->range_max;
        $this->rangeValue = $range->value;
        $this->rangeLabel = $range->label ?? '';
    }

    public function deleteRange(int $id): void
    {
        $qualId = QualificationRange::findOrFail($id)->qualification_id;
        QualificationRange::where('id', $id)->delete();
        $this->selectedQualId = $qualId;
    }

    public function cancelEditRange(): void
    {
        $this->editingRangeId = null;
        $this->rangeMin = '';
        $this->rangeMax = '';
        $this->rangeValue = 0;
        $this->rangeLabel = '';
    }
}
