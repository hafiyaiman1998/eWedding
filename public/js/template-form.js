document.addEventListener('DOMContentLoaded', function() {
    console.log('=== TEMPLATE-FORM.JS LOADED ===');
    // Code syntax highlighting for template editor
    const codeAreas = document.querySelectorAll('.form-code');
    codeAreas.forEach(area => {
        area.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 4;
            }
        });
    });
    
    // Preview image display
    const imageInput = document.getElementById('preview_image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create preview if doesn't exist
                    let preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'image-preview';
                        preview.style.marginTop = '10px';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #e2e8f0;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Parse variables from template
    const parseBtn = document.getElementById('parseVariablesBtn');
    const forceGalleryBtn = document.getElementById('forceGalleryBtn');
    const templateInput = document.getElementById('full_html_template');
    const variablesSection = document.getElementById('defaultVariablesSection');
    const variableFields = document.getElementById('variableFields');
    const hiddenInput = document.getElementById('default_variables_json');
    
    console.log('BUTTONS FOUND:');
    console.log('parseBtn:', parseBtn);
    console.log('forceGalleryBtn:', forceGalleryBtn);
    console.log('templateInput:', templateInput);
    console.log('variablesSection:', variablesSection);
    console.log('variableFields:', variableFields);
    console.log('hiddenInput:', hiddenInput);
    
    // Load existing variables on page load (for edit form)
    if (typeof loadExistingVariables !== 'undefined') {
        loadExistingVariables();
    }
    
    if (parseBtn) {
        console.log('ADDING PARSE BUTTON LISTENER');
        parseBtn.addEventListener('click', function() {
            console.log('PARSE BUTTON CLICKED!');
            const templateCode = templateInput.value;
            
            if (!templateCode.trim()) {
                alert('Please enter template code first');
                return;
            }
            
            // Extract variables from template code
            const variables = extractVariables(templateCode);
            
            if (variables.length === 0) {
                alert('No variables found in template code. Make sure to use format like {{ $details["variable_name"] }}');
                return;
            }
            
            // Set flag to indicate parse variables was used
            const parseUsedFlag = document.getElementById('parse_variables_used');
            if (parseUsedFlag) {
                parseUsedFlag.value = '1';
            }
            
            // Create form fields for each variable
            createVariableFields(variables);
            
            // Show the variables section (for create form)
            console.log('variablesSection before:', variablesSection.style.display);
            if (variablesSection.style.display === 'none') {
                variablesSection.style.display = 'block';
            }
            // Force show section
            variablesSection.style.display = 'block';
            variablesSection.style.visibility = 'visible';
            console.log('variablesSection after:', variablesSection.style.display);
            
            // Scroll to variables section
            variablesSection.scrollIntoView({ behavior: 'smooth' });
        });
    }
    
    // Force add gallery photos button
    if (forceGalleryBtn) {
        console.log('ADDING FORCE GALLERY BUTTON LISTENER');
        forceGalleryBtn.addEventListener('click', function() {
            console.log('FORCE GALLERY BUTTON CLICKED!');
            
            // Get existing variables first
            const templateCode = templateInput.value;
            let variables = [];
            
            if (templateCode.trim()) {
                variables = extractVariables(templateCode);
            }
            
            // Force add gallery_photo_1 through gallery_photo_6
            console.log('FORCING gallery_photo_1 through gallery_photo_6');
            for (let i = 1; i <= 6; i++) {
                const galleryVar = `gallery_photo_${i}`;
                if (!variables.includes(galleryVar)) {
                    variables.push(galleryVar);
                    console.log(`FORCE ADDED: ${galleryVar}`);
                }
            }
            
            if (variables.length === 0) {
                alert('Please enter template code first, or at least add basic variables');
                return;
            }
            
            // Create form fields
            createVariableFields(variables);
            
            // Show the variables section
            if (variablesSection.style.display === 'none') {
                variablesSection.style.display = 'block';
            }
            
            // Scroll to variables section
            variablesSection.scrollIntoView({ behavior: 'smooth' });
            
            alert('Successfully added gallery_photo_1 through gallery_photo_6 inputs!');
        });
    }
    
    function extractVariables(templateCode) {
        const variables = new Set();
        
        console.log('=== VARIABLE EXTRACTION DEBUG ===');
        console.log('Template code length:', templateCode.length);
        console.log('First 500 chars:', templateCode.substring(0, 500));
        
        // Step 1: Extract all regular $details variables
        const detailsPattern = /\{\{\s*\$details\[\s*["']([^"'$]+)["']\s*\]\s*(?:\?\?\s*[^}]+)?\s*\}\}/g;
        let match;
        
        console.log('\n--- Regular Variables ---');
        while ((match = detailsPattern.exec(templateCode)) !== null) {
            const varName = match[1];
            console.log('Found regular variable:', varName);
            variables.add(varName);
        }
        
        // Step 2: Look for @for loops to find iteration ranges
        const forLoopPattern = /@for\s*\(\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(\d+)\s*;\s*\$[a-zA-Z_][a-zA-Z0-9_]*\s*<=?\s*(\d+)/g;
        const loopRanges = new Map();
        
        console.log('\n--- Loop Detection ---');
        while ((match = forLoopPattern.exec(templateCode)) !== null) {
            const iteratorVar = match[1];
            const startNum = parseInt(match[2]);
            const endNum = parseInt(match[3]);
            loopRanges.set(iteratorVar, { start: startNum, end: endNum });
            console.log(`Found @for loop: $${iteratorVar} from ${startNum} to ${endNum}`);
        }
        
        // Step 3: Find variables that use loop iterators in two main patterns
        console.log('\n--- Loop Variable Detection ---');
        
        // Pattern A: {{ $details['variable_' . $i] }}
        const concatenatedPattern = /\{\{\s*\$details\[\s*["']([^"']+)_["']\s*\.\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\]/g;
        while ((match = concatenatedPattern.exec(templateCode)) !== null) {
            const baseName = match[1];
            const iteratorVar = match[2];
            const range = loopRanges.get(iteratorVar) || { start: 1, end: 6 };
            
            console.log(`Pattern A found: ${baseName}_ with iterator $${iteratorVar}`);
            console.log(`Generating ${baseName}_${range.start} through ${baseName}_${range.end}`);
            
            for (let i = range.start; i <= range.end; i++) {
                const loopVar = `${baseName}_${i}`;
                variables.add(loopVar);
                console.log(`  -> Added: ${loopVar}`);
            }
        }
        
        // Pattern B: $details["variable_$i"] (inside @if conditions)
        const interpolationPattern = /\$details\[\s*["']([^"']+)_\$([a-zA-Z_][a-zA-Z0-9_]*)["']\s*\]/g;
        while ((match = interpolationPattern.exec(templateCode)) !== null) {
            const baseName = match[1];
            const iteratorVar = match[2];
            const range = loopRanges.get(iteratorVar) || { start: 1, end: 6 };
            
            console.log(`Pattern B found: ${baseName}_ with iterator $${iteratorVar}`);
            console.log(`Generating ${baseName}_${range.start} through ${baseName}_${range.end}`);
            
            for (let i = range.start; i <= range.end; i++) {
                const loopVar = `${baseName}_${i}`;
                variables.add(loopVar);
                console.log(`  -> Added: ${loopVar}`);
            }
        }
        
        // Step 4: SIMPLE GALLERY DETECTION - Just check for gallery_photo + @for
        console.log('\n--- Simple Gallery Detection ---');
        if (templateCode.includes('gallery_photo')) {
            console.log('Found gallery_photo in template');
            
            // Look for @for loop with numbers to get the range
            const forMatch = templateCode.match(/@for\s*\(\s*\$\w+\s*=\s*(\d+)\s*;\s*\$\w+\s*<=?\s*(\d+)/);
            let startNum = 1;
            let endNum = 6;
            
            if (forMatch) {
                startNum = parseInt(forMatch[1]);
                endNum = parseInt(forMatch[2]);
                console.log(`Found @for loop range: ${startNum} to ${endNum}`);
            } else {
                console.log('No @for loop found, using default range 1 to 6');
            }
            
            console.log(`Adding gallery_photo_${startNum} through gallery_photo_${endNum}`);
            for (let i = startNum; i <= endNum; i++) {
                const galleryVar = `gallery_photo_${i}`;
                variables.add(galleryVar);
                console.log(`  -> FORCED ADD: ${galleryVar}`);
            }
                 }
         
         // Step 5: ABSOLUTE FALLBACK - If we see ANY @for loop, add gallery photos
         console.log('\n--- Absolute Fallback ---');
         if (!Array.from(variables).some(v => v.startsWith('gallery_photo_'))) {
             const anyForLoop = templateCode.match(/@for\s*\(\s*\$\w+\s*=\s*(\d+)\s*;\s*\$\w+\s*<=?\s*(\d+)/);
             if (anyForLoop) {
                 const startNum = parseInt(anyForLoop[1]);
                 const endNum = parseInt(anyForLoop[2]);
                 console.log(`No gallery photos detected but found @for loop ${startNum} to ${endNum}`);
                 console.log('Adding gallery_photo variables as fallback');
                 
                 for (let i = startNum; i <= endNum; i++) {
                     const galleryVar = `gallery_photo_${i}`;
                     variables.add(galleryVar);
                     console.log(`  -> FALLBACK ADD: ${galleryVar}`);
                 }
             }
         }
         
         const result = Array.from(variables).sort();
        console.log('\n=== FINAL RESULTS ===');
        console.log('All variables found:', result);
        console.log('Total count:', result.length);
        console.log('Gallery variables:', result.filter(v => v.startsWith('gallery_photo_')));
        console.log('========================');
        
        return result;
    }
    
    function loadExistingVariables() {
        console.log('=== LOAD EXISTING VARIABLES CALLED ===');
        // Load existing variables from the template (for edit form)
        const templateCode = templateInput.value;
        const existingData = JSON.parse(hiddenInput.value || '{}');
        
        console.log('Template code length:', templateCode.length);
        console.log('Existing data:', existingData);
        
        if (templateCode) {
            let variables = extractVariables(templateCode);
            
            // FORCE ADD GALLERY PHOTOS - ALWAYS
            console.log('FORCING GALLERY PHOTOS TO BE ADDED');
            const galleryVars = ['gallery_photo_1', 'gallery_photo_2', 'gallery_photo_3', 'gallery_photo_4', 'gallery_photo_5', 'gallery_photo_6'];
            galleryVars.forEach(gVar => {
                if (!variables.includes(gVar)) {
                    variables.push(gVar);
                    console.log('FORCED ADDED:', gVar);
                }
            });
            
            console.log('Final variables list:', variables);
            
            if (variables.length > 0) {
                createVariableFields(variables, existingData);
                console.log('createVariableFields called with', variables.length, 'variables');
            } else {
                console.log('NO VARIABLES TO CREATE!');
            }
        } else {
            console.log('NO TEMPLATE CODE FOUND');
        }
    }
    
    function isPhotoVariable(variableName) {
        const photoKeywords = ['photo', 'image', 'picture', 'pic', 'avatar', 'background', 'logo', 'icon', 'poster', 'story'];
        return photoKeywords.some(keyword => variableName.toLowerCase().includes(keyword));
    }

    function isVideoVariable(variableName) {
        const videoKeywords = ['video', 'movie', 'clip', 'film', 'mp4', 'webm', 'mov'];
        const varLower = variableName.toLowerCase();
        
        // Exclude video poster variables - they should be treated as images
        if (varLower.includes('poster')) {
            return false;
        }
        
        return videoKeywords.some(keyword => varLower.includes(keyword));
    }

    function isAudioVariable(variableName) {
        const audioKeywords = ['audio', 'music', 'sound', 'mp3', 'wav', 'ogg'];
        const varLower = variableName.toLowerCase();
        
        // Special handling for song URLs
        if (varLower.includes('song') && varLower.includes('url')) {
            return true;
        }
        
        return audioKeywords.some(keyword => varLower.includes(keyword));
    }

    function isDateVariable(variableName) {
        const varLower = variableName.toLowerCase();
        
        // Variables that specifically contain 'date' (but not 'datetime')
        return varLower.includes('date') && !varLower.includes('datetime') && !varLower.includes('time');
    }

    function isDateTimeVariable(variableName) {
        const varLower = variableName.toLowerCase();
        
        // Variables that contain 'datetime' or both 'date' and 'time'
        return varLower.includes('datetime') || (varLower.includes('date') && varLower.includes('time'));
    }

    function isTimeVariable(variableName) {
        const varLower = variableName.toLowerCase();
        const timeKeywords = ['time', 'hour', 'minute'];
        
        // Exclude datetime and date variables
        if (varLower.includes('date')) {
            return false;
        }
        
        return timeKeywords.some(keyword => varLower.includes(keyword));
    }
    
    function createVariableFields(variables, existingData = {}) {
        console.log('=== CREATE VARIABLE FIELDS CALLED ===');
        console.log('Variables to create:', variables);
        console.log('Existing data:', existingData);
        console.log('variableFields element:', variableFields);
        
        if (!variableFields) {
            console.error('variableFields element not found!');
            return;
        }
        
        variableFields.innerHTML = '';
        
        // Group loop variables for better display
        const groupedVariables = groupLoopVariables(variables);
        console.log('Grouped variables:', groupedVariables);
        
        // Display grouped variables
        groupedVariables.forEach((group, index) => {
            console.log(`Processing group ${index}:`, group);
            if (group.isLoop) {
                console.log('Creating loop section for:', group.baseName);
                // Create a section for loop variables
                const loopSection = document.createElement('div');
                loopSection.className = 'loop-variable-section';
                loopSection.style.marginBottom = '20px';
                loopSection.style.padding = '15px';
                loopSection.style.backgroundColor = '#f8fafc';
                loopSection.style.border = '1px solid #e2e8f0';
                loopSection.style.borderRadius = '8px';
                
                const loopTitle = document.createElement('h4');
                loopTitle.style.margin = '0 0 15px 0';
                loopTitle.style.color = '#2d3748';
                loopTitle.style.fontSize = '16px';
                loopTitle.innerHTML = `<i class="fas fa-repeat"></i> ${group.baseName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())} (Loop Variables)`;
                loopSection.appendChild(loopTitle);
                
                group.variables.forEach(variable => {
                    console.log(`Creating field for loop variable: ${variable}`);
                    createSingleVariableField(loopSection, variable, existingData);
                });
                
                console.log('Appending loop section to variableFields');
                console.log('Loop section HTML:', loopSection.outerHTML);
                variableFields.appendChild(loopSection);
                console.log('Current variableFields HTML:', variableFields.innerHTML);
            } else {
                // Create individual variable field
                group.variables.forEach(variable => {
                    createSingleVariableField(variableFields, variable, existingData);
                });
            }
        });
        
        // Initial JSON update
        updateDefaultVariablesJson();
    }
    
    function groupLoopVariables(variables) {
        const groups = [];
        const loopGroups = new Map();
        const singleVariables = [];
        
        variables.forEach(variable => {
            // Check if this is a loop variable (ends with _number)
            const loopMatch = variable.match(/^(.+)_(\d+)$/);
            if (loopMatch) {
                const baseName = loopMatch[1];
                if (!loopGroups.has(baseName)) {
                    loopGroups.set(baseName, []);
                }
                loopGroups.get(baseName).push(variable);
            } else {
                singleVariables.push(variable);
            }
        });
        
        // Add single variables first
        if (singleVariables.length > 0) {
            groups.push({
                isLoop: false,
                variables: singleVariables
            });
        }
        
        // Add loop groups
        for (const [baseName, loopVars] of loopGroups) {
            if (loopVars.length > 1) { // Only group if there are multiple
                groups.push({
                    isLoop: true,
                    baseName: baseName,
                    variables: loopVars.sort((a, b) => {
                        const aNum = parseInt(a.split('_').pop());
                        const bNum = parseInt(b.split('_').pop());
                        return aNum - bNum;
                    })
                });
            } else {
                // If only one variable, treat as single
                groups[0] = groups[0] || { isLoop: false, variables: [] };
                groups[0].variables.push(...loopVars);
            }
        }
        
        return groups;
    }
    
    function createSingleVariableField(container, variable, existingData) {
        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'variable-field';
        
        const label = document.createElement('div');
        label.className = 'variable-label';
        label.textContent = variable.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        const inputContainer = document.createElement('div');
        inputContainer.className = 'variable-input-container';
        
        if (isDateTimeVariable(variable)) {
            // Create datetime-local input for datetime variables
            const input = document.createElement('input');
            input.type = 'datetime-local';
            input.className = 'variable-input';
            input.dataset.variable = variable;
            input.placeholder = `Select date and time for ${variable}`;
            
            // Set existing value if available
            if (existingData[variable]) {
                // Try to format the date for datetime-local input
                try {
                    const date = new Date(existingData[variable]);
                    if (!isNaN(date.getTime())) {
                        // Format to YYYY-MM-DDTHH:MM format required by datetime-local
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');
                        input.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    } else {
                        input.value = existingData[variable];
                    }
                } catch (e) {
                    input.value = existingData[variable];
                }
            }
            
            input.addEventListener('change', updateDefaultVariablesJson);
            inputContainer.appendChild(input);
            
        } else if (isDateVariable(variable)) {
            // Create date input for date variables
            const input = document.createElement('input');
            input.type = 'date';
            input.className = 'variable-input';
            input.dataset.variable = variable;
            input.placeholder = `Select date for ${variable}`;
            
            // Set existing value if available
            if (existingData[variable]) {
                // Try to format the date for date input
                try {
                    const date = new Date(existingData[variable]);
                    if (!isNaN(date.getTime())) {
                        // Format to YYYY-MM-DD format required by date input
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        input.value = `${year}-${month}-${day}`;
                    } else {
                        input.value = existingData[variable];
                    }
                } catch (e) {
                    input.value = existingData[variable];
                }
            }
            
            input.addEventListener('change', updateDefaultVariablesJson);
            inputContainer.appendChild(input);
            
        } else if (isTimeVariable(variable)) {
            // Create time input for time variables
            const input = document.createElement('input');
            input.type = 'time';
            input.className = 'variable-input';
            input.dataset.variable = variable;
            input.placeholder = `Select time for ${variable}`;
            
            // Set existing value if available
            if (existingData[variable]) {
                input.value = existingData[variable];
            }
            
            input.addEventListener('change', updateDefaultVariablesJson);
            inputContainer.appendChild(input);
            
        } else if (isPhotoVariable(variable)) {
            // Create file upload for photo variables
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.className = 'variable-file-input';
            fileInput.accept = 'image/*';
            fileInput.dataset.variable = variable;
            
            const urlInput = document.createElement('input');
            urlInput.type = 'text';
            urlInput.className = 'variable-input';
            urlInput.placeholder = `URL for ${variable} (will be auto-filled on upload)`;
            urlInput.dataset.variable = variable;
            urlInput.readonly = true;
            
            // Set existing value if available
            if (existingData[variable]) {
                urlInput.value = existingData[variable];
            }
            
            // Handle file upload
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Get the current URL to delete old file
                    const oldUrl = urlInput.value && urlInput.value !== 'Uploading...' ? urlInput.value : '';
                    
                    // Show loading state
                    urlInput.value = 'Uploading...';
                    urlInput.style.color = '#718096';
                    
                    // Create FormData for upload
                    const formData = new FormData();
                    formData.append('photo', file);
                    formData.append('variable_name', variable);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
                    
                    // Add old URL for deletion if exists
                    if (oldUrl) {
                        formData.append('old_url', oldUrl);
                    }
                    
                    // Upload file
                    fetch('/admin/templates/upload-variable-photo', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            urlInput.value = data.url;
                            urlInput.style.color = '#2d3748';
                            updateDefaultVariablesJson();
                            
                            // Show preview if it's an image
                            showImagePreview(inputContainer, data.url);
                        } else {
                            alert('Upload failed: ' + (data.message || 'Unknown error'));
                            urlInput.value = existingData[variable] || '';
                            urlInput.style.color = '#2d3748';
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        alert('Upload failed. Please try again.');
                        urlInput.value = existingData[variable] || '';
                        urlInput.style.color = '#2d3748';
                    });
                }
            });
            
            // Show existing image preview if URL exists
            if (existingData[variable]) {
                showImagePreview(inputContainer, existingData[variable]);
            }
            
            inputContainer.appendChild(fileInput);
            inputContainer.appendChild(urlInput);
            
        } else if (isVideoVariable(variable)) {
            // Create file upload for video variables
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.className = 'variable-file-input';
            fileInput.accept = 'video/*';
            fileInput.dataset.variable = variable;
            
            const urlInput = document.createElement('input');
            urlInput.type = 'text';
            urlInput.className = 'variable-input';
            urlInput.placeholder = `URL for ${variable} (will be auto-filled on upload)`;
            urlInput.dataset.variable = variable;
            urlInput.readonly = true;
            
            // Set existing value if available
            if (existingData[variable]) {
                urlInput.value = existingData[variable];
            }
            
            // Handle file upload
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Get the current URL to delete old file
                    const oldUrl = urlInput.value && urlInput.value !== 'Uploading...' ? urlInput.value : '';
                    
                    // Show loading state
                    urlInput.value = 'Uploading...';
                    urlInput.style.color = '#718096';
                    
                    // Create FormData for upload
                    const formData = new FormData();
                    formData.append('video', file);
                    formData.append('variable_name', variable);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
                    
                    // Add old URL for deletion if exists
                    if (oldUrl) {
                        formData.append('old_url', oldUrl);
                    }
                    
                    // Upload file
                    fetch('/admin/templates/upload-variable-video', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            urlInput.value = data.url;
                            urlInput.style.color = '#2d3748';
                            updateDefaultVariablesJson();
                            
                            // Show preview if it's a video
                            showVideoPreview(inputContainer, data.url);
                        } else {
                            alert('Upload failed: ' + (data.message || 'Unknown error'));
                            urlInput.value = existingData[variable] || '';
                            urlInput.style.color = '#2d3748';
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        alert('Upload failed. Please try again.');
                        urlInput.value = existingData[variable] || '';
                        urlInput.style.color = '#2d3748';
                    });
                }
            });
            
            // Show existing video preview if URL exists
            if (existingData[variable]) {
                showVideoPreview(inputContainer, existingData[variable]);
            }
            
            inputContainer.appendChild(fileInput);
            inputContainer.appendChild(urlInput);
            
        } else if (isAudioVariable(variable)) {
            // Create file upload for audio/song variables
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.className = 'variable-file-input';
            fileInput.accept = 'audio/*,.mp3,.wav,.ogg';
            fileInput.dataset.variable = variable;
            
            const urlInput = document.createElement('input');
            urlInput.type = 'text';
            urlInput.className = 'variable-input';
            urlInput.placeholder = `URL for ${variable} (will be auto-filled on upload)`;
            urlInput.dataset.variable = variable;
            urlInput.readonly = true;
            
            // Set existing value if available
            if (existingData[variable]) {
                urlInput.value = existingData[variable];
            }
            
            // Handle file upload
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Get the current URL to delete old file
                    const oldUrl = urlInput.value && urlInput.value !== 'Uploading...' ? urlInput.value : '';
                    
                    // Show loading state
                    urlInput.value = 'Uploading...';
                    urlInput.style.color = '#718096';
                    
                    // Create FormData for upload
                    const formData = new FormData();
                    formData.append('audio', file);
                    formData.append('variable_name', variable);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
                    
                    // Add old URL for deletion if exists
                    if (oldUrl) {
                        formData.append('old_url', oldUrl);
                    }
                    
                    // Upload file
                    fetch('/admin/templates/upload-variable-audio', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            urlInput.value = data.url;
                            urlInput.style.color = '#2d3748';
                            updateDefaultVariablesJson();
                            
                            // Show audio preview
                            showAudioPreview(inputContainer, data.url);
                        } else {
                            alert('Upload failed: ' + (data.message || 'Unknown error'));
                            urlInput.value = existingData[variable] || '';
                            urlInput.style.color = '#2d3748';
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        alert('Upload failed. Please try again.');
                        urlInput.value = existingData[variable] || '';
                        urlInput.style.color = '#2d3748';
                    });
                }
            });
            
            // Show existing audio preview if URL exists
            if (existingData[variable]) {
                showAudioPreview(inputContainer, existingData[variable]);
            }
            
            inputContainer.appendChild(fileInput);
            inputContainer.appendChild(urlInput);
            
        } else {
            // Create regular text input for non-photo variables
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'variable-input';
            input.placeholder = `Default value for ${variable}`;
            input.dataset.variable = variable;
            
            // Set existing value or common default values
            if (existingData[variable]) {
                input.value = existingData[variable];
            } else {
                const defaultValues = {
                    'bride_name': 'Siti Fatimah',
                    'groom_name': 'Ahmad Bin Abdullah',
                    'bride_full_name': 'Siti Fatimah Binti Mohammad',
                    'groom_full_name': 'Ahmad Bin Abdullah',
                    'wedding_date': '15 Ogos 2024',
                    'wedding_time': '10:00 Pagi',
                    'venue': 'Dewan Serbaguna Kampung',
                    'bride_parents': 'Mohammad Bin Hassan & Khadijah Binti Ahmad',
                    'groom_parents': 'Abdullah Bin Ibrahim & Zainab Binti Ali',
                    'akad_date': '14 Ogos 2024',
                    'akad_time': '9:00 Pagi',
                    'reception_date': '15 Ogos 2024',
                    'reception_time': '7:00 Malam',
                    'wedding_invitation_video': 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                    'video_poster': 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1920&h=1080&fit=crop',
                    'video_subtitle': 'A Special Message for You',
                    // Song defaults
                    'song_1_title': 'Perfect',
                    'song_1_artist': 'Ed Sheeran',
                    'song_1_url': '/audio/perfect.mp3',
                    'song_2_title': 'All of Me',
                    'song_2_artist': 'John Legend',
                    'song_2_url': '/audio/all-of-me.mp3',
                    'song_3_title': 'Thinking Out Loud',
                    'song_3_artist': 'Ed Sheeran',
                    'song_3_url': '/audio/thinking-out-loud.mp3',
                    'song_4_title': 'A Thousand Years',
                    'song_4_artist': 'Christina Perri',
                    'song_4_url': '/audio/thousand-years.mp3',
                    'song_5_title': 'Marry Me',
                    'song_5_artist': 'Train',
                    'song_5_url': '/audio/marry-me.mp3'
                };
                
                if (defaultValues[variable]) {
                    input.value = defaultValues[variable];
                }
            }
            
            // Update JSON when input changes
            input.addEventListener('input', updateDefaultVariablesJson);
            
            inputContainer.appendChild(input);
        }
        
        fieldDiv.appendChild(label);
        fieldDiv.appendChild(inputContainer);
        container.appendChild(fieldDiv);
    }
    
    function showImagePreview(container, imageUrl) {
        // Remove existing preview
        const existingPreview = container.querySelector('.image-preview');
        if (existingPreview) {
            existingPreview.remove();
        }
        
        // Create new preview
        const preview = document.createElement('div');
        preview.className = 'image-preview';
        preview.style.marginTop = '10px';
        
        const img = document.createElement('img');
        img.src = imageUrl;
        img.style.maxWidth = '150px';
        img.style.maxHeight = '100px';
        img.style.borderRadius = '8px';
        img.style.border = '1px solid #e2e8f0';
        img.style.objectFit = 'cover';
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn-remove-image';
        removeBtn.innerHTML = '×';
        removeBtn.style.position = 'absolute';
        removeBtn.style.top = '5px';
        removeBtn.style.right = '5px';
        removeBtn.style.background = 'rgba(239, 68, 68, 0.9)';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '50%';
        removeBtn.style.width = '24px';
        removeBtn.style.height = '24px';
        removeBtn.style.cursor = 'pointer';
        removeBtn.style.display = 'flex';
        removeBtn.style.alignItems = 'center';
        removeBtn.style.justifyContent = 'center';
        removeBtn.style.fontSize = '16px';
        removeBtn.style.lineHeight = '1';
        
        removeBtn.addEventListener('click', function() {
            const urlInput = container.querySelector('.variable-input');
            const currentUrl = urlInput.value;
            
            console.log('=== DELETE FILE ATTEMPT ===');
            console.log('Current URL:', currentUrl);
            console.log('URL contains /storage/:', currentUrl.includes('/storage/'));
            
            // Only delete files from our storage
            if (currentUrl && currentUrl.includes('/storage/')) {
                // Show loading state
                removeBtn.innerHTML = '...';
                removeBtn.disabled = true;
                
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('CSRF Token:', token ? 'Found' : 'Missing');
                
                // Create FormData for deletion
                const formData = new FormData();
                formData.append('file_url', currentUrl);
                formData.append('_token', token || '');
                formData.append('_method', 'DELETE');
                
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                // Delete file from server
                console.log('Making fetch request to:', '/admin/templates/delete-variable-file');
                fetch('/admin/templates/delete-variable-file', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Response error text:', text);
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response data:', data);
                    if (data.success) {
                        console.log('File deleted successfully');
                        // Clear the URL input and remove preview
                        urlInput.value = '';
                        updateDefaultVariablesJson();
                        preview.remove();
                    } else {
                        console.error('Delete failed:', data.message);
                        alert('Failed to delete file: ' + (data.message || 'Unknown error'));
                        removeBtn.innerHTML = '×';
                        removeBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('Failed to delete file: ' + error.message);
                    removeBtn.innerHTML = '×';
                    removeBtn.disabled = false;
                });
            } else {
                console.log('External URL - just clearing input');
                // For external URLs or empty values, just clear the input
                urlInput.value = '';
                updateDefaultVariablesJson();
                preview.remove();
            }
        });
        
        preview.style.position = 'relative';
        preview.style.display = 'inline-block';
        preview.appendChild(img);
        preview.appendChild(removeBtn);
        
        container.appendChild(preview);
    }
    
    function showVideoPreview(container, videoUrl) {
        // Remove existing preview
        const existingPreview = container.querySelector('.video-preview');
        if (existingPreview) {
            existingPreview.remove();
        }
        
        // Create new preview
        const preview = document.createElement('div');
        preview.className = 'video-preview';
        preview.style.marginTop = '10px';
        
        const video = document.createElement('video');
        video.src = videoUrl;
        video.controls = true;
        video.style.maxWidth = '100%';
        video.style.maxHeight = '100px';
        video.style.borderRadius = '8px';
        video.style.border = '1px solid #e2e8f0';
        video.style.objectFit = 'cover';
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn-remove-video';
        removeBtn.innerHTML = '×';
        removeBtn.style.position = 'absolute';
        removeBtn.style.top = '5px';
        removeBtn.style.right = '5px';
        removeBtn.style.background = 'rgba(239, 68, 68, 0.9)';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '50%';
        removeBtn.style.width = '24px';
        removeBtn.style.height = '24px';
        removeBtn.style.cursor = 'pointer';
        removeBtn.style.display = 'flex';
        removeBtn.style.alignItems = 'center';
        removeBtn.style.justifyContent = 'center';
        removeBtn.style.fontSize = '16px';
        removeBtn.style.lineHeight = '1';
        
        removeBtn.addEventListener('click', function() {
            const urlInput = container.querySelector('.variable-input');
            const currentUrl = urlInput.value;
            
            console.log('=== DELETE VIDEO FILE ATTEMPT ===');
            console.log('Current URL:', currentUrl);
            console.log('URL contains /storage/:', currentUrl.includes('/storage/'));
            
            // Only delete files from our storage
            if (currentUrl && currentUrl.includes('/storage/')) {
                // Show loading state
                removeBtn.innerHTML = '...';
                removeBtn.disabled = true;
                
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('CSRF Token:', token ? 'Found' : 'Missing');
                
                // Create FormData for deletion
                const formData = new FormData();
                formData.append('file_url', currentUrl);
                formData.append('_token', token || '');
                formData.append('_method', 'DELETE');
                
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                // Delete file from server
                console.log('Making fetch request to:', '/admin/templates/delete-variable-file');
                fetch('/admin/templates/delete-variable-file', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);
                    
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Response error text:', text);
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response data:', data);
                    if (data.success) {
                        console.log('Video file deleted successfully');
                        // Clear the URL input and remove preview
                        urlInput.value = '';
                        updateDefaultVariablesJson();
                        preview.remove();
                    } else {
                        console.error('Delete failed:', data.message);
                        alert('Failed to delete file: ' + (data.message || 'Unknown error'));
                        removeBtn.innerHTML = '×';
                        removeBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('Failed to delete file: ' + error.message);
                    removeBtn.innerHTML = '×';
                    removeBtn.disabled = false;
                });
            } else {
                console.log('External URL - just clearing input');
                // For external URLs or empty values, just clear the input
                urlInput.value = '';
                updateDefaultVariablesJson();
                preview.remove();
            }
        });
        
        preview.style.position = 'relative';
        preview.style.display = 'inline-block';
        preview.appendChild(video);
        preview.appendChild(removeBtn);
        
        container.appendChild(preview);
    }
    
    function showAudioPreview(container, audioUrl) {
        // Remove existing preview
        const existingPreview = container.querySelector('.audio-preview');
        if (existingPreview) {
            existingPreview.remove();
        }
        
        // Create new preview
        const preview = document.createElement('div');
        preview.className = 'audio-preview';
        preview.style.marginTop = '10px';
        
        const audio = document.createElement('audio');
        audio.src = audioUrl;
        audio.controls = true;
        audio.style.width = '100%';
        audio.style.maxWidth = '300px';
        audio.style.height = '40px';
        audio.style.borderRadius = '8px';
        audio.style.border = '1px solid #e2e8f0';
        audio.style.background = '#f8f9fa';
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn-remove-audio';
        removeBtn.innerHTML = '×';
        removeBtn.style.position = 'absolute';
        removeBtn.style.top = '5px';
        removeBtn.style.right = '5px';
        removeBtn.style.background = 'rgba(239, 68, 68, 0.9)';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '50%';
        removeBtn.style.width = '24px';
        removeBtn.style.height = '24px';
        removeBtn.style.cursor = 'pointer';
        removeBtn.style.display = 'flex';
        removeBtn.style.alignItems = 'center';
        removeBtn.style.justifyContent = 'center';
        removeBtn.style.fontSize = '16px';
        removeBtn.style.lineHeight = '1';
        
        removeBtn.addEventListener('click', function() {
            const urlInput = container.querySelector('.variable-input');
            const currentUrl = urlInput.value;
            
            console.log('=== DELETE AUDIO FILE ATTEMPT ===');
            console.log('Current URL:', currentUrl);
            console.log('URL contains /storage/:', currentUrl.includes('/storage/'));
            
            // Only delete files from our storage
            if (currentUrl && currentUrl.includes('/storage/')) {
                // Show loading state
                removeBtn.innerHTML = '...';
                removeBtn.disabled = true;
                
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('CSRF Token:', token ? 'Found' : 'Missing');
                
                // Create FormData for deletion
                const formData = new FormData();
                formData.append('file_url', currentUrl);
                formData.append('_token', token || '');
                formData.append('_method', 'DELETE');
                
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                // Delete file from server
                console.log('Making fetch request to:', '/admin/templates/delete-variable-file');
                fetch('/admin/templates/delete-variable-file', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);
                    
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Response error text:', text);
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response data:', data);
                    if (data.success) {
                        console.log('Audio file deleted successfully');
                        // Clear the URL input and remove preview
                        urlInput.value = '';
                        updateDefaultVariablesJson();
                        preview.remove();
                    } else {
                        console.error('Delete failed:', data.message);
                        alert('Failed to delete audio file: ' + (data.message || 'Unknown error'));
                        removeBtn.innerHTML = '×';
                        removeBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('Failed to delete audio file: ' + error.message);
                    removeBtn.innerHTML = '×';
                    removeBtn.disabled = false;
                });
            } else {
                console.log('External URL - just clearing input');
                // For external URLs or empty values, just clear the input
                urlInput.value = '';
                updateDefaultVariablesJson();
                preview.remove();
            }
        });
        
        preview.style.position = 'relative';
        preview.style.display = 'inline-block';
        preview.appendChild(audio);
        preview.appendChild(removeBtn);
        
        container.appendChild(preview);
    }
    
    function updateDefaultVariablesJson() {
        const inputs = variableFields.querySelectorAll('.variable-input');
        const data = {};
        
        inputs.forEach(input => {
            const variable = input.dataset.variable;
            const value = input.value.trim();
            
            if (value && value !== 'Uploading...') {
                data[variable] = value;
            }
        });
        
        hiddenInput.value = JSON.stringify(data);
    }
    
    // Make functions available globally for edit form
    window.loadExistingVariables = loadExistingVariables;
    window.extractVariables = extractVariables;
    window.createVariableFields = createVariableFields;
    window.updateDefaultVariablesJson = updateDefaultVariablesJson;
}); 