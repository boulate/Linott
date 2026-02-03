/**
 * Alpine.js component for cascading axes selector with bidirectional selection
 *
 * Features:
 * - Descendant filtering: selecting a parent filters all descendant levels
 * - Ancestor auto-fill: selecting a child automatically fills all ancestors
 * - Deselection: clears children but keeps parents
 */
function periodeAxesSelector(config) {
    return {
        // Raw data for lookups
        allSections: config.sections || [],
        allAxes1: config.axes1 || [],
        allAxes2: config.axes2 || [],
        allAxes3: config.axes3 || [],

        // Current selections (as strings for select compatibility)
        selectedSection: String(config.initialSection || ''),
        selectedAxe1: String(config.initialAxe1 || ''),
        selectedAxe2: String(config.initialAxe2 || ''),
        selectedAxe3: String(config.initialAxe3 || ''),

        init() {
            // Apply initial filtering
            this.$nextTick(() => {
                this.filterAxe1Options();
                this.filterAxe2Options();
                this.filterAxe3Options();
            });
        },

        // Helper to find parent chain from data arrays
        getAxe1ById(id) {
            return this.allAxes1.find(a => a.id == id);
        },

        getAxe2ById(id) {
            return this.allAxes2.find(a => a.id == id);
        },

        getAxe3ById(id) {
            return this.allAxes3.find(a => a.id == id);
        },

        // Get Axe1 IDs that belong to selected section
        getAxe1IdsForSection(sectionId) {
            if (!sectionId) return null; // null means no filter
            return this.allAxes1
                .filter(a => a.sectionId == sectionId)
                .map(a => a.id);
        },

        // Get Axe2 IDs that belong to selected axe1 or section
        getAxe2IdsForFilters() {
            if (this.selectedAxe1) {
                return this.allAxes2
                    .filter(a => a.axe1Id == this.selectedAxe1)
                    .map(a => a.id);
            }
            if (this.selectedSection) {
                const axe1Ids = this.getAxe1IdsForSection(this.selectedSection);
                return this.allAxes2
                    .filter(a => axe1Ids.includes(a.axe1Id))
                    .map(a => a.id);
            }
            return null; // no filter
        },

        // Get Axe3 IDs based on current filters
        getAxe3IdsForFilters() {
            if (this.selectedAxe2) {
                return this.allAxes3
                    .filter(a => a.axe2Id == this.selectedAxe2)
                    .map(a => a.id);
            }
            if (this.selectedAxe1) {
                const axe2Ids = this.allAxes2
                    .filter(a => a.axe1Id == this.selectedAxe1)
                    .map(a => a.id);
                return this.allAxes3
                    .filter(a => axe2Ids.includes(a.axe2Id))
                    .map(a => a.id);
            }
            if (this.selectedSection) {
                const axe1Ids = this.getAxe1IdsForSection(this.selectedSection);
                const axe2Ids = this.allAxes2
                    .filter(a => axe1Ids.includes(a.axe1Id))
                    .map(a => a.id);
                return this.allAxes3
                    .filter(a => axe2Ids.includes(a.axe2Id))
                    .map(a => a.id);
            }
            return null; // no filter
        },

        // Filter options in a select element
        filterSelectOptions(selectRef, allowedIds, dataAttr) {
            if (!this.$refs[selectRef]) return;

            const select = this.$refs[selectRef];
            const options = select.querySelectorAll('option[' + dataAttr + ']');

            options.forEach(option => {
                if (allowedIds === null) {
                    // No filter - show all
                    option.hidden = false;
                    option.disabled = false;
                } else {
                    const id = parseInt(option.value);
                    const allowed = allowedIds.includes(id);
                    option.hidden = !allowed;
                    option.disabled = !allowed;
                }
            });
        },

        filterAxe1Options() {
            const allowedIds = this.getAxe1IdsForSection(this.selectedSection);
            this.filterSelectOptions('axe1Select', allowedIds, 'data-section');
        },

        filterAxe2Options() {
            const allowedIds = this.getAxe2IdsForFilters();
            this.filterSelectOptions('axe2Select', allowedIds, 'data-axe1');
        },

        filterAxe3Options() {
            const allowedIds = this.getAxe3IdsForFilters();
            this.filterSelectOptions('axe3Select', allowedIds, 'data-axe2');
        },

        // Event handlers
        onSelectSection(value) {
            this.selectedSection = value;

            // Check if current axe1 is still valid
            if (this.selectedAxe1) {
                const axe1 = this.getAxe1ById(this.selectedAxe1);
                if (axe1 && value && axe1.sectionId != value) {
                    this.selectedAxe1 = '';
                    this.selectedAxe2 = '';
                    this.selectedAxe3 = '';
                }
            }

            this.filterAxe1Options();
            this.filterAxe2Options();
            this.filterAxe3Options();
        },

        onSelectAxe1(value) {
            this.selectedAxe1 = value;

            if (value) {
                // Retropropagate: fill parent
                const axe1 = this.getAxe1ById(value);
                if (axe1) {
                    this.selectedSection = String(axe1.sectionId);
                }
            }

            // Check if current axe2 is still valid
            if (this.selectedAxe2) {
                const axe2 = this.getAxe2ById(this.selectedAxe2);
                if (axe2 && value && axe2.axe1Id != value) {
                    this.selectedAxe2 = '';
                    this.selectedAxe3 = '';
                }
            }

            this.filterAxe1Options();
            this.filterAxe2Options();
            this.filterAxe3Options();
        },

        onSelectAxe2(value) {
            this.selectedAxe2 = value;

            if (value) {
                // Retropropagate: fill parents
                const axe2 = this.getAxe2ById(value);
                if (axe2) {
                    this.selectedAxe1 = String(axe2.axe1Id);
                    const axe1 = this.getAxe1ById(axe2.axe1Id);
                    if (axe1) {
                        this.selectedSection = String(axe1.sectionId);
                    }
                }
            }

            // Check if current axe3 is still valid
            if (this.selectedAxe3) {
                const axe3 = this.getAxe3ById(this.selectedAxe3);
                if (axe3 && value && axe3.axe2Id != value) {
                    this.selectedAxe3 = '';
                }
            }

            this.filterAxe1Options();
            this.filterAxe2Options();
            this.filterAxe3Options();
        },

        onSelectAxe3(value) {
            this.selectedAxe3 = value;

            if (value) {
                // Retropropagate: fill all parents
                const axe3 = this.getAxe3ById(value);
                if (axe3) {
                    this.selectedAxe2 = String(axe3.axe2Id);
                    const axe2 = this.getAxe2ById(axe3.axe2Id);
                    if (axe2) {
                        this.selectedAxe1 = String(axe2.axe1Id);
                        const axe1 = this.getAxe1ById(axe2.axe1Id);
                        if (axe1) {
                            this.selectedSection = String(axe1.sectionId);
                        }
                    }
                }
            }

            this.filterAxe1Options();
            this.filterAxe2Options();
            this.filterAxe3Options();
        }
    };
}
