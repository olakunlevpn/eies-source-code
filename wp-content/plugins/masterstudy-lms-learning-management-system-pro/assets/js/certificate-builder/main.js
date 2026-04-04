(function($) {
    $(document).ready(function() {
        const jsPDF = window.jspdf.jsPDF;
        new Vue({
            el: '#masterstudy-certificate-builder',
            components: {
                VueDraggableResizable,
                QrcodeVue,
                'photoshop-picker': VueColor.Photoshop
            },
            data() {
                return {
                    isAdmin: masterstudy_certificate_data.is_admin,
                    certificates: [],
                    currentCertificate: 0,
                    generatedPreviews: masterstudy_certificate_data.not_generated_previews,
                    defaultCertificate: '',
                    categoryCertificate: '',
                    newDefaultCertificate: '',
                    newCategoryCertificate: '',
                    certificateSearchQuery: '',
                    categoryToSave: '',
                    titleActive: false,
                    activeField: null,
                    activeControlsTab: 'elements',
                    openControlsSidebar: false,
                    createPopupVisible: false,
                    deletePopupVisible: false,
                    imagePopupVisible: false,
                    certificateForDelete: '',
                    categoryForDelete: '',
                    deleteAlertType: 'certificate',
                    notImageError: false,
                    notImageErrorField: false,
                    certificatesPopupVisible: false,
                    categoriesPopupVisible: false,
                    fonts: [],
                    fields: [],
                    field: {
                        styles: {
                            fontFamily: "Arial",
                        },
                    },
                    fieldsCategories: ['certificate','course','student','instructor'],
                    categories: [],
                    googleFontsList: [],
                    fontsLoaded: false,
                    totalCategories: 0,
                    categoriesLimit: 10,
                    searchCategories: '',
                    searchCategoriesActive: false,
                    currentCategoriesPage: 1,
                    loadingCategoriesPage: false,
                    loadingSaveButton: false,
                    previewLoading: false,
                    certificateSaved: false,
                    colorPickerVisible: false,
                    currentTab: 'builder',
                    currentDestinationTab: 'default',
                    selectDropdownOpen: false,
                    selectedInstructorName: '',
                    selectedInstructorId: null,
                    dropdownShapeOpen: false,
                    shapes: masterstudy_certificate_data.shapes,
                    certificate_page: masterstudy_certificate_data.certificate_page,
                    qrImage: masterstudy_certificate_data.qr_image_url,
                    ActiveShapefield: null,
                    ActiveShapefieldKey: null,
                    activeFieldShapeElement: null,
                    refreshKey: 0,
                    offsetIndex: 0,
                    maxOffsetSteps: 10,
                    offsetStep: 20,
                    offsetResetTimer: null,
                    offsetResetDelay: 4000,
                }
            },
            mounted() {
                Promise.all([this.getAllCertificates(), this.getFields()]).then(() => {
                    if (masterstudy_certificate_data.googleFonts) {
                        this.googleFontsList = masterstudy_certificate_data.googleFonts;
                    }
                    const loader = document.querySelector('.masterstudy-loader_global');
                    if (loader) {
                        loader.style.display = 'none';
                    }
                    this.goToTab();
                    this.goToCertificate();
                    this.loadGoogleFonts();
                    this.setDefaultFont();

                    if (Array.isArray(this.certificates) && this.certificates.length > 0) {
                        this.certificates.forEach(certificate => {
                            this.renderAndSendCertificate(certificate);
                        });
                    }
                })
                document.addEventListener('click', this.handleClickOutside);
                document.addEventListener('click', this.handleClickOutsideShapeSettings);
                if (this.isAdmin) {
                    this.getCategories();
                }
            },
            computed: {
                savedCertificates() {
                    return this.certificates.filter(certificate => certificate.id);
                },
                filteredCertificates() {
                    if (this.categoriesPopupVisible) {
                        return this.savedCertificates.filter(certificate =>
                          certificate?.data?.category.length < 1
                        );
                      } else {
                        return this.savedCertificates;
                      }
                },
                certificatesList() {
                    const filteredByInstructor = this.selectedInstructorId
                    ? this.certificates.filter(certificate => certificate.author && certificate.author.id === this.selectedInstructorId)
                    : this.certificates;

                    return filteredByInstructor.filter(certificate => {
                        const title = certificate.title ? certificate.title.toLowerCase() : '';
                        const id = certificate.id ? certificate.id.toString() : '';
                        const searchQueryLower = this.certificateSearchQuery.toLowerCase();

                        return title.includes(searchQueryLower) || id.includes(this.certificateSearchQuery);
                    });
                },
                categoriesWithCertificates() {
                    return this.categories.map(category => {
                        const certificate = this.certificates.find(certificate => {
                            const categoriesArray = certificate.data?.category.split(',') || [];
                            return categoriesArray.includes(String(category.id));
                        });
                        return {
                            ...category,
                            certificate: certificate || null,
                        };
                    });
                },
                totalCategoriesPages() {
                    if (this.totalCategories > 0) {
                        return Math.ceil(this.totalCategories / this.categoriesLimit);
                    }
                },
                categoriesPagesToShow() {
                    const maxPages = 5;
                    const middlePage = Math.floor(maxPages / 2);
                    let startPage = Math.max(this.currentCategoriesPage - middlePage, 1);
                    let endPage = Math.min(startPage + maxPages - 1, this.totalCategoriesPages);

                    if (endPage - startPage < maxPages - 1) {
                        startPage = Math.max(endPage - maxPages + 1, 1);
                    }

                    const pages = [];
                    for (let i = startPage; i <= endPage; i++) {
                        pages.push(i);
                    }

                    return pages;
                },
                categoriesShowPrev() {
                    return this.currentCategoriesPage > 1;
                },
                categoriesShowNext() {
                    return this.currentCategoriesPage < this.totalCategoriesPages;
                },
                uniqueInstructors() {
                    const instructorsMap = new Map();
                    this.certificates.forEach(certificate => {
                        if (certificate.author && !instructorsMap.has(certificate.author.id)) {
                            instructorsMap.set(certificate.author.id, certificate.author.name);
                        }
                    });
                    return Array.from(instructorsMap, ([id, name]) => ({ id, name }));
                },
            },
            watch: {
                currentCertificate(newValue, oldValue) {
                    this.addActualTitle();
                    if (!this.fontsLoaded) {
                        this.$nextTick(() => {
                            setTimeout(() => {
                                let newSelects = document.querySelectorAll(".selectGFont");
                                if (newSelects.length) {
                                    newSelects.forEach(select => this.loadGoogleFonts(select));
                                }
                            }, 50);
                        });
                    }
                },

            },
            methods: {
                async processCertificates(certificates) {
                    for (const data of certificates) {
                        await renderAndSendCertificate(data);
                    }
                },
                async renderAndSendCertificate(data) {
                    if (!data?.data?.fields || !Array.isArray(data.data.fields)) {
                        return;
                    }

                    if (data.image) {
                        return;
                    }

                    let container = document.createElement('div');
                    container.classList.add('certificate-preview');

                    let orientation = data.data.orientation
                    if (data.thumbnail) {
                        let bgImage = document.createElement('img');
                        bgImage.src = data.thumbnail;
                        bgImage.style.width = '100%';
                        bgImage.style.height = '100%';
                        bgImage.style.position = 'absolute';
                        bgImage.style.top = '0';
                        bgImage.style.left = '0';

                        container.style.width = orientation === 'portrait' ? '589px' : '898px';
                        container.style.height = orientation === 'portrait' ? '898px' : '589px';
                        container.style.position = 'relative';

                        container.appendChild(bgImage);
                    }

                    let qrPromises = [];

                    data.data.fields.forEach(field => {
                        if (!field) return;

                        let element;

                        if (field.type === 'image') {
                            element = document.createElement('img');
                            element.src = field.content || '';
                            element.style.width = field.w + 'px'
                            element.style.height = field.h + 'px'
                        } else if (field.type === 'qrcode') {
                            let tempDiv = document.createElement('div');
                            document.body.appendChild(tempDiv);

                            qrPromises.push(new Promise(resolve => {
                                new QRCode(tempDiv, {
                                    text: field.content || '',
                                    width: field.w || 100,
                                    height: field.h || 100,
                                    colorDark: "#000000",
                                    colorLight: "#ffffff",
                                    correctLevel: QRCode.CorrectLevel.H
                                });

                                setTimeout(() => {
                                    let qrCanvas = tempDiv.querySelector('canvas');
                                    if (qrCanvas) {
                                        let qrImage = document.createElement('img');
                                        qrImage.src = qrCanvas.toDataURL("image/png");
                                        qrImage.style.width = (field.w || 100) + 'px';
                                        qrImage.style.height = (field.h || 100) + 'px';
                                        qrImage.style.position = 'absolute';
                                        qrImage.style.left = (field.x || 0) + 'px';
                                        qrImage.style.top = (field.y || 0) + 'px';
                                        container.appendChild(qrImage);
                                    }
                                    document.body.removeChild(tempDiv);
                                    resolve();
                                }, 100);
                            }));

                            return;
                        } else {
                            element = document.createElement('div');
                            element.textContent = field.content || '';
                            element.style.fontSize = field.styles?.fontSize || '16px';
                            element.style.color = field.styles?.color?.hex || '#000';
                            element.style.textAlign = field.styles?.textAlign || 'left';
                            element.style.fontFamily = field.styles?.fontFamily || 'Arial';
                            element.style.fontWeight = field.styles?.fontWeight ? 'bold' : 'normal';
                            element.style.fontStyle = field.styles?.fontStyle ? 'italic' : 'normal';
                        }

                        element.style.position = 'absolute';
                        element.style.left = (field.x || 0) + 'px';
                        element.style.top = (field.y || 0) + 'px';
                        element.style.width = (field.w || 100) + 'px'
                        container.appendChild(element);
                    });

                    document.body.appendChild(container);
                    container.style.position = "absolute";
                    container.style.left = "-9999px";

                    await Promise.all(qrPromises);
                    await new Promise(resolve => setTimeout(resolve, 500));

                    try {
                        const canvas = await html2canvas(container, {
                            scale: 3,
                            useCORS: true,
                        });
                        canvas.toBlob(async (blob) => {
                            if (!blob) return;

                            let formData = new FormData();
                            formData.append('preview', blob, 'preview.jpg');
                            formData.append('certificate', JSON.stringify(data));
                            formData.append('action', 'stm_save_certificate');
                            formData.append('nonce', stm_lms_nonces['stm_save_certificate']);

                            try {
                                const response = await fetch(stm_lms_ajaxurl, {
                                    method: 'POST',
                                    body: formData,
                                });
                                await response.json();

                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    let previewContainer = document.querySelector(`label[data-id="${data.id}"] .masterstudy-certificate-templates__item-image`);
                                    if (previewContainer) {
                                        let previewImg = previewContainer.querySelector('img');
                                        if (!previewImg) {
                                            previewImg = document.createElement('img');
                                            previewContainer.appendChild(previewImg);
                                        }
                                        previewImg.src = e.target.result;
                                    }
                                };
                                reader.readAsDataURL(blob);

                            } catch (error) {
                                console.error(`❌ Certificate save error ${data.id}:`, error);
                            }
                        }, 'image/jpeg');
                    } catch (error) {
                        console.error(`❌ Certificate render error ${data.id}:`, error);
                    }

                    document.body.removeChild(container);
                },
                setDefaultFont() {
                    if (!this.certificates || !this.certificates[this.currentCertificate]) return;

                    let excludedTypes = ["image", "qrcode", "shape", "grades"];
                    let fontMap = {};

                    this.certificates[this.currentCertificate].data.fields.forEach((field, key) => {
                        if (excludedTypes.includes(field.type) || !field.styles || !field.styles.fontFamily) return;

                        fontMap[key] = field.styles.fontFamily;
                    });

                    this.$nextTick(() => {
                        let selectRefs = this.$refs.selectGFont;

                        if (!selectRefs) return;

                        Object.keys(fontMap).forEach((key, index) => {
                            if (selectRefs[index]) {
                                selectRefs[index].setAttribute("data-default", fontMap[key]);
                            }
                        });
                    });
                },
                onFontSelect() {
                    let select = this.$refs.selectGFont;
                    if (!select) return;

                    jQuery(select).off("select2:select").on("select2:select", (event) => {
                        let selectedFont = event.params.data.view;

                        if (this.activeField === null || this.activeField === undefined) return;

                        let field = this.certificates[this.currentCertificate].data.fields[this.activeField];
                        if (!field || !field.styles) return;

                        this.$set(field.styles, "fontFamily", selectedFont);
                    });
                },
                async loadGoogleFonts(select = null) {
                    this.$nextTick(() => {
                        if (typeof selectGfont === "undefined") {
                            return;
                        }

                        if (select) {
                            if (select.dataset.fontsLoaded === "true") {
                                return;
                            }
                            selectGfont({
                                googleFontsJson: this.googleFontsList,
                                containerFonte: select,
                            });
                            select.dataset.fontsLoaded = "true";
                            this.onFontSelect();
                            return;
                        }

                        let selects = document.querySelectorAll(".selectGFont");
                        if (!selects.length) {
                            return;
                        }

                        selects.forEach((s) => {
                            if (s.dataset.fontsLoaded === "true") {
                                return;
                            }
                            selectGfont({
                                googleFontsJson: this.googleFontsList,
                                containerFonte: s,
                            });
                            s.dataset.fontsLoaded = "true";
                        });

                        this.onFontSelect();
                    });
                },
                updateContent(event, field) {
                    const selection = window.getSelection();
                    const range = selection.getRangeAt(0);
                    const startOffset = range.startOffset;
                    field.content = event.target.innerText;

                    this.$nextTick(() => {
                        const newRange = document.createRange();
                        newRange.setStart(event.target.childNodes[0] || event.target, startOffset);
                        newRange.collapse(true);
                        selection.removeAllRanges();
                        selection.addRange(newRange);
                    });
                },
                handleClickOutside(event) {
                    if (this.$refs.titleContainer && !this.$refs.titleContainer.contains(event.target)) {
                      this.titleActive = false;
                    }
                    const vcPhotoshopContainer = document.querySelector('.vc-photoshop');
                    if ( vcPhotoshopContainer ) {
                        if (!document.querySelector('.vc-photoshop').contains(event.target) && !this.isEventInsideColor(event.target)) {
                            this.colorPickerVisible = false;
                        }
                    }
                },
                isEventInsideColor(eventTarget) {
                    let node = eventTarget;
                    while (node != null) {
                        if (node.matches && node.matches('.font-style .color')) {
                            return true;
                        }
                        node = node.parentNode;
                    }
                    return false;
                },
                toggleSelectDropdown() {
                    this.selectDropdownOpen = !this.selectDropdownOpen;
                },
                selectInstructor(instructor) {
                    if (instructor) {
                        this.selectedInstructorId = instructor.id;
                        this.selectedInstructorName = instructor.name;
                    } else {
                        this.selectedInstructorId = null;
                        this.selectedInstructorName = '';
                    }
                    this.selectDropdownOpen = false;
                },
                goToTab() {
                    const url = new URL(window.location);
                    const tab = url.searchParams.get('tab');
                    const destination = url.searchParams.get('destination');
                    if (tab) {
                        this.currentTab = tab;
                    }
                    if (destination) {
                        this.currentDestinationTab = destination;
                    }
                },
                goToCertificate() {
                    const url = new URL(window.location);
                    const current = url.searchParams.get('certificate_id');
                    if (current) {
                        const index = this.certificates.findIndex(certificate => certificate.id === parseInt(current));
                        if (index !== -1) {
                            this.currentCertificate = index;
                        }
                        this.$nextTick(() => {
                            const contentContainer = document.querySelector('.masterstudy-certificate-templates__content');
                            const certificateElement = document.querySelector(`.masterstudy-certificate-templates__item[data-id="${current}"]`);
                            if (certificateElement && contentContainer) {
                                const elementTop = certificateElement.offsetTop;
                                contentContainer.scrollTop = elementTop - 155;
                            }
                        });
                    }
                },
                changeCurrentTab(tab, openPopup = false) {
                    this.currentTab = tab;
                    this.addActualTitle();
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tab);
                    window.history.pushState({ path: url.toString() }, '', url.toString());
                    if (openPopup) {
                        const _this = this;
                        setTimeout(function() {
                            _this.openCreatePopup();
                        }, 200);
                    }
                },
                changeCurrentDestinationTab(tab) {
                    this.currentDestinationTab = tab;
                    const url = new URL(window.location);
                    url.searchParams.set('destination', tab);
                    window.history.pushState({ path: url.toString() }, '', url.toString());
                },
                addActualTitle() {
                    const currentTitle = this.certificates[this.currentCertificate]?.title;
                    this.$nextTick(() => {
                        if (this.$refs.editCertificateTitle && currentTitle !== undefined) {
                            this.$refs.editCertificateTitle.textContent = currentTitle;
                        }
                    });
                },
                colorPickerShow() {
                    this.colorPickerVisible = true;
                },
                getAllCertificates() {
                    const url = stm_lms_ajaxurl + '?action=stm_get_certificates&nonce=' + stm_lms_nonces['stm_get_certificates'];
                    return this.$http.get(url).then((response) => {
                        this.$set(this, 'certificates', response.body);
                        this.addActualTitle();
                        this.getDefaultCertificate();
                    });
                },
                previewCertificate(event) {
                    this.previewLoading = true;
                    const id = event.currentTarget.getAttribute('data-id');
                    if (id) {
                        this.getCertificate(id);
                    }
                },
                getCertificate(id) {
                    const url = stm_lms_ajaxurl + '?action=stm_get_certificate&nonce=' + stm_lms_nonces['stm_get_certificate'] + '&post_id=' + id;
                    this.$http.get(url).then((response) => {
                        if (typeof response.body.data !== 'undefined') {
                            this.generateCertificate(response.body.data, true);
                            this.renderAndSendCertificate(response.body.data, false)
                        }
                    });
                },
                generateCertificate(certificate, preview) {
                    return new Promise((resolve, reject) => {
                        this.$nextTick(() => {
                            const orientation = preview ? certificate.orientation : certificate.data.orientation;
                            const isPortrait = orientation === 'portrait';

                            const width = isPortrait ? 794 : 1123;
                            const height = isPortrait ? 1123 : 794;

                            const doc = new jsPDF({
                                orientation: orientation,
                                unit: 'px',
                                format: [width, height],
                            });

                            const background = certificate.preview;

                            if (background) {
                                doc.addImage(background, "JPEG", 0, 0, width, height, '', 'NONE');
                            }

                            if (preview) {
                                const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
                                if (isSafari) {
                                    doc.autoPrint();
                                    doc.output('save', 'Certificate.pdf');
                                    resolve();
                                } else {
                                    window.open(doc.output('bloburl'));
                                    resolve();
                                }
                            } else {
                                resolve(doc.output('blob'));
                            }

                            this.previewLoading = false;
                        });
                    });
                },
                applyColorToSvg(svg, fieldIndex) {
                    const currentField = this.certificates[this.currentCertificate].data.fields[fieldIndex];
                    if (currentField.type !== 'shape') {
                        return;
                    }
                    const color = currentField.styles.color.hex || '#808C98';
                    const svgWithColor = svg.replace(/<path /g, `<path fill="${color}" `);
                    return svgWithColor;
                },
                convertSvgToImage(svgContent, containerWidth, containerHeight, newColor, scaleFactor = 2) {
                    return new Promise((resolve, reject) => {
                        let updatedSvgContent = svgContent.replace(/(<path[^>]*?)fill="[^"]*"/g, `$1fill="${newColor}"`);
                        const svgBlob = new Blob([updatedSvgContent], { type: 'image/svg+xml' });
                        const url = URL.createObjectURL(svgBlob);
                        const img = new Image();

                        img.onload = () => {
                            const originalWidth = img.width;
                            const originalHeight = img.height;
                            const adjustedWidth = containerWidth * scaleFactor;
                            const adjustedHeight = containerHeight * scaleFactor;
                            const scalingFactor = Math.min(adjustedWidth / originalWidth, adjustedHeight / originalHeight);
                            const newWidth = originalWidth * scalingFactor;
                            const newHeight = originalHeight * scalingFactor;

                            const canvas = document.createElement('canvas');
                            canvas.width = adjustedWidth;
                            canvas.height = adjustedHeight;

                            const context = canvas.getContext('2d');
                            context.clearRect(0, 0, adjustedWidth, adjustedHeight);
                            const offsetX = (adjustedWidth - newWidth) / 2;
                            const offsetY = (adjustedHeight - newHeight) / 2;

                            context.drawImage(img, offsetX, offsetY, newWidth, newHeight);
                            const base64Image = canvas.toDataURL('image/png', 1.0);
                            resolve(base64Image);
                        };
                        img.onerror = (error) => {
                            reject(new Error("Error converting SVG to PNG"));
                        };
                        img.src = url;
                    });
                },
                hexToRGB(h) {
                    let r = 0, g = 0, b = 0;

                    if (h.length == 4) {
                        r = "0x" + h[1] + h[1];
                        g = "0x" + h[2] + h[2];
                        b = "0x" + h[3] + h[3];
                    } else if (h.length == 7) {
                        r = "0x" + h[1] + h[2];
                        g = "0x" + h[3] + h[4];
                        b = "0x" + h[5] + h[6];
                    }

                    return {
                        r: r,
                        g: g,
                        b: b,
                    };
                },
                getDefaultCertificate() {
                    const defaultCertificate = this.certificates.find(certificate =>
                        certificate.id === parseInt(masterstudy_certificate_data.default_certificate)
                    );
                    if (defaultCertificate) {
                        this.defaultCertificate = defaultCertificate;
                        this.newDefaultCertificate = defaultCertificate;
                    }
                },
                getFields() {
                    const url = stm_lms_ajaxurl + '?action=stm_get_certificate_fields&nonce=' + stm_lms_nonces['stm_get_certificate_fields'];
                    return this.$http.get(url).then(function(res) {
                        this.$set(this, 'fields', res.body);
                    });
                },
                prevCategoriesPage(event) {
                    if (event.currentTarget.classList.contains('masterstudy-pagination__button_disabled')) {
                        return;
                    }
                    if (this.currentCategoriesPage > 1) {
                        this.currentCategoriesPage--;
                        this.getCategories(this.currentCategoriesPage);
                    }
                },
                nextCategoriesPage(event) {
                    if (event.currentTarget.classList.contains('masterstudy-pagination__button_disabled')) {
                        return;
                    }
                    if (this.currentCategoriesPage < this.totalCategoriesPages) {
                        this.currentCategoriesPage++;
                        this.getCategories(this.currentCategoriesPage);
                    }
                },
                changeCategoriesPage(page) {
                    this.currentCategoriesPage = page;
                    this.getCategories(this.currentCategoriesPage);
                },
                getCategories(page = 1, searchActive = false) {
                    this.loadingCategoriesPage = true;
                    const offset = (page - 1) * this.categoriesLimit;
                    const url = `${stm_lms_ajaxurl}?action=stm_get_certificate_categories&nonce=${stm_lms_nonces['stm_get_certificate_categories']}&offset=${offset}&search=${this.searchCategories}`;
                    this.$http.get(url).then(function(res) {
                        this.$set(this, 'categories', res.body.categories);
                        this.$set(this, 'totalCategories', res.body.total);
                        this.loadingCategoriesPage = false;
                        if ( searchActive ) {
                            this.searchCategoriesActive = true;
                            this.currentCategoriesPage = 1;
                        }
                    });
                },
                resetCategoriesSearch() {
                    this.searchCategories = '';
                    this.searchCategoriesActive = false;
                    this.currentCategoriesPage = 1;
                    this.getCategories();
                },
                resetCertificatesSearch() {
                    this.certificateSearchQuery = '';
                },
                openCreatePopup() {
                    this.createPopupVisible = true;
                },
                closeCreatePopup() {
                    this.createPopupVisible = false;
                },
                openImagePopup() {
                    this.imagePopupVisible = true;
                },
                openDeletePopupDefault() {
                    this.deleteAlertType = 'default';
                    this.deletePopupVisible = true;
                },
                openDeletePopupCertificate(index) {
                    this.deleteAlertType = 'certificate';
                    this.certificateForDelete = index;
                    this.deletePopupVisible = true;
                },
                openDeletePopupCategory(certificate, category) {
                    this.deleteAlertType = 'category';
                    this.certificateForDelete = certificate;
                    this.categoryForDelete = category;
                    this.deletePopupVisible = true;
                },
                closeDeletePopup() {
                    this.deletePopupVisible = false;
                },
                closeImagePopup() {
                    this.imagePopupVisible = false;
                },
                openCategoriesPopup(certificate, category) {
                    this.categoriesPopupVisible = true;
                    this.categoryCertificate = certificate;
                    this.newCategoryCertificate = certificate;
                    this.categoryToSave = category;
                },
                openCertificatesPopup() {
                    this.certificatesPopupVisible = true;
                    this.newDefaultCertificate = this.defaultCertificate;
                },
                closeCategoriesPopup() {
                    document.querySelector('.masterstudy-certificate-select-popup').classList.remove('masterstudy-certificate-select-popup_open');
                    const _this = this;
                    setTimeout(function() {
                        _this.categoriesPopupVisible = false;
                        _this.categoryCertificate = '';
                        _this.categoryToSave = '';
                        _this.newCategoryCertificate = '';
                    }, 300);
                },
                closeCategoriesPopupWithRedirect() {
                    this.closeCategoriesPopup();
                    this.changeCurrentTab('builder', true);
                },
                closeCertificatesPopup() {
                    const _this = this;
                    _this.certificatesPopupVisible = false;
                    setTimeout(function() {
                        _this.newDefaultCertificate = _this.defaultCertificate;
                    }, 300);
                },
                createPopupWrapperClick(event) {
                    if (!event.target.closest('.masterstudy-certificate-create-popup__wrapper')) {
                        this.closeCreatePopup();
                    }
                },
                deletePopupWrapperClick(event) {
                    if (!event.target.closest('.masterstudy-certificate-delete-popup__wrapper')) {
                        this.closeDeletePopup();
                    }
                },
                imagePopupWrapperClick(event) {
                    if (!event.target.closest('.masterstudy-certificate-image-popup__wrapper')) {
                        this.closeImagePopup();
                    }
                },
                certificatesPopupWrapperClick(event) {
                    if (!event.target.closest('.masterstudy-certificate-select-popup__wrapper')) {
                        this.closeCertificatesPopup();
                    }
                },
                categoriesPopupWrapperClick(event) {
                    if (!event.target.closest('.masterstudy-certificate-select-popup__wrapper')) {
                        this.closeCategoriesPopup();
                    }
                },
                editInBuilder(){
                    const index = this.certificates.findIndex(certificate => certificate.id === this.defaultCertificate.id);
                    this.$set(this, 'currentCertificate', index);
                    this.changeCurrentTab('builder');
                },
                addCertificate(orientation_type) {
                    const newCertificate = {
                        id: '',
                        title: 'New template',
                        thumbnail_id: '',
                        thumbnail: '',
                        image: '',
                        filename: '',
                        data: {
                            orientation: orientation_type,
                            category: '',
                            fields: []
                        }
                    };
                    const certificates = this.certificates;
                    certificates.unshift(newCertificate);
                    this.$set(this, 'certificates', certificates);
                    this.$set(this, 'currentCertificate', 0);
                    this.addActualTitle();
                    this.closeCreatePopup();
                    this.$nextTick(() => {
                        const content = document.querySelector('.masterstudy-certificate-templates__content');
                        if (content) {
                            content.scrollTop = 0;
                        }
                    });
                },
                uploadFieldImage(index) {
                    const _this = this;
                    const custom_uploader = wp.media({
                        title: "Select image",
                        button: {
                            text: "Attach"
                        },
                        multiple: true
                    }).on("select", function() {
                        const attachment = custom_uploader.state().get("selection").first().toJSON();
                        if (typeof _this.certificates[_this.currentCertificate].data.fields[index] !== 'undefined') {
                            _this.$set(_this.certificates[_this.currentCertificate].data.fields[index], 'imageId', attachment.id);
                            _this.$set(_this.certificates[_this.currentCertificate].data.fields[index], 'content', attachment.url);
                        }
                    }).open();
                },
                uploadImage() {
                    const _this = this;
                    const custom_uploader = wp.media({
                        title: "Select image",
                        button: {
                            text: "Attach"
                        },
                        multiple: true
                    }).on("select", function() {
                        const attachment = custom_uploader.state().get("selection").first().toJSON();
                        _this.certificates[_this.currentCertificate].thumbnail_id = attachment.id;
                        _this.certificates[_this.currentCertificate].thumbnail = attachment.url;
                        _this.certificates[_this.currentCertificate].filename = attachment.filename;
                    }).open();
                },
                handleFileUpload(event, background, index = '') {
                    const _this = this;
                    if (background) {
                        _this.notImageError = false;
                    } else {
                        _this.notImageErrorField = false;
                    }
                    if (event.target.files.length > 0) {
                        if (!event.target.files[0].type.startsWith('image/')) {
                            if (background) {
                                _this.notImageError = true;
                            } else {
                                _this.notImageErrorField = true;
                            }
                            return;
                        }
                        let formData = new FormData();
                        formData.append('action', 'stm_upload_certificate_images');
                        formData.append('image', event.target.files[0]);
                        formData.append('nonce', stm_lms_nonces['stm_upload_certificate_images']);

                        _this.$http.post(stm_lms_ajaxurl, formData, {}).then(function(response) {
                            if(response.body.image){
                                if (background) {
                                    _this.certificates[_this.currentCertificate].thumbnail_id = response.body.image.id;
                                    _this.certificates[_this.currentCertificate].thumbnail = response.body.image.url;
                                    _this.certificates[_this.currentCertificate].filename = response.body.image.filename;
                                } else {
                                    if (typeof _this.certificates[_this.currentCertificate].data.fields[index] !== 'undefined') {
                                        _this.$set(_this.certificates[_this.currentCertificate].data.fields[index], 'imageId', response.body.image.id);
                                        _this.$set(_this.certificates[_this.currentCertificate].data.fields[index], 'content', response.body.image.url);
                                    }
                                }
                            }
                        });
                    }
                },
                deleteImage(event) {
                    event.currentTarget.classList.add('masterstudy-button_loading');
                    this.certificates[this.currentCertificate].thumbnail_id = '';
                    this.certificates[this.currentCertificate].thumbnail = '';
                    this.certificates[this.currentCertificate].filename = '';
                    event.target.classList.remove('masterstudy-button_loading');
                    const anchor = event.target.closest('a');
                    if (anchor) {
                        anchor.classList.remove('masterstudy-button_loading');
                    }
                    this.closeImagePopup();
                },
                addField(event, type, fieldToDuplicate = null) {
                    if (event && event.currentTarget.classList.contains('masterstudy-certificate-controls__item_disabled')) {
                        return;
                    }

                    const orientation = this.certificates[this.currentCertificate]?.data?.orientation || 'landscape';
                    let defaultX = orientation === 'portrait' ? 225 : 375;
                    let defaultY = 0;

                    let content = this.fields[type]?.value || '';
                    let height = 50;
                    let width = 150;
                    let zIndex = 2;
                    let styles = {
                        fontSize: '14px',
                        fontFamily: 'Montserrat',
                        color: { hex: '#000' },
                        textAlign: 'left',
                        fontStyle: 'normal',
                        fontWeight: '400',
                    };

                    if (type === 'image') height = 150;
                    if (type === 'shape') {
                        height = 100;
                        width = 100;
                        styles = { color: { hex: '#808C98' } };
                    }
                    if (type === 'grades') {
                        height = 317;
                        width = 355;
                    }
                    if (type === 'qrcode') {
                        height = 100;
                        width = 100;
                        styles = {};
                    }

                    if (fieldToDuplicate) {
                        content = fieldToDuplicate.content;
                        height = fieldToDuplicate.h;
                        width = fieldToDuplicate.w;
                        zIndex = fieldToDuplicate.z;
                        styles = { ...fieldToDuplicate.styles };
                        defaultX = fieldToDuplicate.x;
                        defaultY = fieldToDuplicate.y;
                    }

                    const fields = this.certificates[this.currentCertificate]?.data?.fields || [];

                    const offset = this.offsetIndex < this.maxOffsetSteps
                        ? this.offsetIndex * this.offsetStep
                        : (this.maxOffsetSteps - 1) * this.offsetStep;

                    let newX = defaultX + offset;
                    let newY = defaultY + offset;

                    if (this.offsetIndex < this.maxOffsetSteps) {
                        this.offsetIndex++;
                    }

                    if (this.offsetResetTimer) {
                        clearTimeout(this.offsetResetTimer);
                    }

                    this.offsetResetTimer = setTimeout(() => {
                        this.offsetIndex = 0;
                        this.offsetResetTimer = null;
                    }, this.offsetResetDelay);

                    const newField = {
                        type: type,
                        content: content,
                        x: newX,
                        y: newY,
                        w: width,
                        h: height,
                        styles: styles,
                        classes: 'top-align',
                        z: zIndex,
                    };

                    if (fields) {
                        fields.push(newField);
                    } else {
                        this.$set(this.certificates[this.currentCertificate].data, 'fields', [newField]);
                    }

                    this.$nextTick(() => {
                        setTimeout(() => {
                            let newSelects = document.querySelectorAll(".selectGFont");
                            let lastSelect = newSelects[newSelects.length - 1];
                            if (lastSelect) {
                                this.loadGoogleFonts(lastSelect);
                            }
                        }, 50);
                    });
                },
                duplicateField(index) {
                    const fieldToDuplicate = this.certificates[this.currentCertificate].data.fields[index];
                    if (fieldToDuplicate) {
                        this.addField(null, fieldToDuplicate.type, fieldToDuplicate);
                    }
                },
                increaseZIndex(index) {
                    const field = this.certificates[this.currentCertificate].data.fields[index];
                    if (field.z < 50) {
                        this.$set(field, 'z', field.z + 1);
                    }
                },
                decreaseZIndex(index) {
                    const field = this.certificates[this.currentCertificate].data.fields[index];
                    if (field.z > 1) {
                        this.$set(field, 'z', field.z - 1);
                    }
                },
                deleteCertificate(event, index) {
                    event.currentTarget.classList.add('masterstudy-button_loading');
                    const certificates = this.certificates;
                    if (typeof certificates[index] !== 'undefined') {
                        if(typeof certificates[index].id !== 'undefined'){
                            const url = stm_lms_ajaxurl + '?action=stm_delete_certificate&nonce=' + stm_lms_nonces['stm_delete_certificate'] + '&certificate_id=' + certificates[index].id;
                            this.$http.get(url).then(function (res) {
                                certificates.splice(index, 1);
                                this.$set(this, 'certificates', certificates);
                                if (index !== 0) {
                                    this.$set(this, 'currentCertificate', index-1);
                                }
                                this.addActualTitle();
                                this.updateDefaultCertificate();
                                event.target.classList.remove('masterstudy-button_loading');
                                const anchor = event.target.closest('a');
                                if (anchor) {
                                    anchor.classList.remove('masterstudy-button_loading');
                                }
                                this.closeDeletePopup();
                                this.$nextTick(() => {
                                    setTimeout(() => {
                                        let newSelects = document.querySelectorAll(".selectGFont");
                                        if (newSelects.length) {
                                            newSelects.forEach(select => this.loadGoogleFonts(select));
                                        }
                                    }, 50);
                                });
                            });
                        }
                    }
                },
                deleteField(index) {
                    const fields = this.certificates[this.currentCertificate].data.fields;
                    if (fields[index].type === 'shape') {
                        this.hideShapeSettings()
                    }
                    if (typeof fields[index] !== 'undefined') {
                        fields.splice(index, 1);
                        this.$set(this.certificates[this.currentCertificate].data, 'fields', fields);
                        this.activeField = null;
                        this.refreshKey += 1;
                    }
                    this.$nextTick(() => {
                        setTimeout(() => {
                            if (fields[index].type === 'shape') {
                                this.hideShapeSettings()
                            }
                            let newSelects = document.querySelectorAll(".selectGFont");
                            if (newSelects.length) {
                                newSelects.forEach(select => this.loadGoogleFonts(select));
                            }
                        }, 50);
                    });
                },
                switchControlsTab(tabName) {
                    this.activeControlsTab = tabName;
                },
                openRightSidebar() {
                    this.openControlsSidebar = !this.openControlsSidebar;
                },
                updateCertificateTitle(newTitle) {
                    this.certificates[this.currentCertificate].title = newTitle;
                },
                addTitleActiveClass() {
                    this.titleActive = true;
                },
                removeTitleActiveClass() {
                    this.titleActive = false;
                },
                focusOnEditCertificate() {
                    const editableDiv = this.$refs.editCertificateTitle;
                    const range = document.createRange();
                    const sel = window.getSelection();
                    const childNodes = editableDiv.childNodes;
                    const lastChild = childNodes[childNodes.length - 1];

                    if (lastChild) {
                        range.selectNodeContents(editableDiv);
                        range.collapse(false);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                    editableDiv.focus();
                },
                async saveCertificate() {
                    const _this = this;
                    _this.loadingSaveButton = true;
                    let changedLang = null

                    if (document.documentElement.lang === 'bg-BG') {
                        changedLang = document.documentElement.lang
                        document.documentElement.lang = 'en-US'
                    }

                    this.hideShapeSettings();

                    try {
                        await this.$nextTick();
                        const certificateElement = document.querySelector('.masterstudy-certificate-canvas-wrap__zone');

                        const vdrElements = certificateElement.querySelectorAll('.vdr');
                        const originalBorderColors = [];
                        vdrElements.forEach((el, index) => {
                            originalBorderColors[index] = el.style.getPropertyValue('border-color');
                            el.style.setProperty('border-color', 'transparent', 'important');
                        });

                        const canvas = await html2canvas(certificateElement, {
                            scale: 3,
                            useCORS: true,
                        });

                        vdrElements.forEach((el, index) => {
                            el.style.setProperty('border-color', originalBorderColors[index]);
                        });

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                return;
                            }

                            let formData = new FormData();

                            formData.append('preview', blob, 'preview.jpg');
                            formData.append('certificate', JSON.stringify(this.certificates[this.currentCertificate]));
                            formData.append('action', 'stm_save_certificate');
                            formData.append('nonce', stm_lms_nonces['stm_save_certificate']);

                            this.$http.post(stm_lms_ajaxurl, formData, {})
                                .then((response) => {
                                    if (typeof response.body.id !== 'undefined') {
                                        this.$set(this.certificates[this.currentCertificate], 'id', response.body.id);
                                        this.$set(this.certificates[this.currentCertificate], 'image', response.body.image);
                                    }
                                    this.loadingSaveButton = false;
                                    this.certificateSaved = true;

                                    setTimeout(() => {
                                        this.certificateSaved = false;
                                    }, 1000);
                                })
                                .catch((error) => {
                                    this.loadingSaveButton = false;
                                }
                            );
                        }, 'image/jpeg');
                    } catch (error) {
                        console.error(error);
                    } finally {
                        if (changedLang) {
                            document.documentElement.lang = changedLang
                        }
                    }
                },
                saveCertificateCategory(event) {
                    if (event.currentTarget.classList.contains('masterstudy-button_disabled')) {
                        return;
                    }
                    this.loadingSaveButton = true;
                    const data = {
                        old_certificate: this.categoryCertificate,
                        new_certificate: this.newCategoryCertificate,
                        category: this.categoryToSave,
                        action: 'stm_save_certificate_category',
                        nonce: stm_lms_nonces['stm_save_certificate_category'],
                    };
                    this.$http.post(stm_lms_ajaxurl, data, {emulateJSON: true}).then(function(response) {
                        if (response.data === "saved") {
                            const _this = this;
                            _this.loadingSaveButton = false;
                            document.querySelector('.masterstudy-certificate-select-popup').classList.remove('masterstudy-certificate-select-popup_open');
                            setTimeout(function() {
                                const certificateToAdd = _this.certificates.find(certificate => certificate.id === _this.newCategoryCertificate.id);
                                if (certificateToAdd.data.category && certificateToAdd.data.category.length > 0) {
                                    let categoriesArray = certificateToAdd.data.category.split(',');
                                    if (!categoriesArray.includes(_this.categoryToSave)) {
                                        categoriesArray.push(_this.categoryToSave);
                                        certificateToAdd.data.category = categoriesArray.join(',');
                                    }
                                } else {
                                    certificateToAdd.data.category = _this.categoryToSave;
                                }
                                if ( _this.categoryCertificate ) {
                                    const certificateToDelete = _this.certificates.find(certificate => certificate.id === _this.categoryCertificate.id);
                                    if (certificateToDelete && certificateToDelete.data) {
                                        let categories = certificateToDelete.data.category.split(',');
                                        categories = categories.filter(categoryId => categoryId !== _this.categoryToSave);
                                        certificateToDelete.data.category = categories.join(',');
                                    }
                                }
                                _this.categoriesPopupVisible = false;
                                _this.categoryCertificate = '';
                                _this.categoryToSave = '';
                                _this.newCategoryCertificate = '';
                            }, 300 );
                        }
                    });
                },
                unlinkCategory(event, certificate, certCategory) {
                    event.currentTarget.classList.add('masterstudy-button_loading');
                    const data = {
                        certificate: certificate,
                        category: certCategory,
                        action: 'stm_delete_certificate_category',
                        nonce: stm_lms_nonces['stm_delete_certificate_category'],
                    };
                    this.$http.post(stm_lms_ajaxurl, data, {emulateJSON: true}).then(function(response) {
                        if (response.data === "saved") {
                            const certificateToDelete = this.certificates.find(certif => certif.id === certificate.id);
                            if (certificateToDelete && certificateToDelete.data) {
                                let categories = certificateToDelete.data.category.split(',');
                                categories = categories.filter(categoryId => categoryId !== certCategory);
                                certificateToDelete.data.category = categories.join(',');
                                this.categoryCertificate = '';
                                this.newCategoryCertificate = '';
                            }
                        }
                        event.target.classList.remove('masterstudy-button_loading');
                        const anchor = event.target.closest('a');
                        if (anchor) {
                            anchor.classList.remove('masterstudy-button_loading');
                        }
                        this.closeDeletePopup();
                    });
                },
                checkDefaultCertificate(certificate) {
                    this.newDefaultCertificate = certificate;
                },
                checkCertificateCategory(certificate) {
                    this.newCategoryCertificate = certificate;
                },
                deleteDefaultCertificate(event) {
                    event.currentTarget.classList.add('masterstudy-button_loading');
                    const data = {
                        action: 'stm_delete_default_certificate',
                        nonce: stm_lms_nonces['stm_delete_default_certificate'],
                    };
                    this.$http.post(stm_lms_ajaxurl, data, {emulateJSON: true}).then(function(response) {
                        if (response.data === "deleted") {
                            this.defaultCertificate = '';
                        }
                        event.target.classList.remove('masterstudy-button_loading');
                        const anchor = event.target.closest('a');
                        if (anchor) {
                            anchor.classList.remove('masterstudy-button_loading');
                        }
                        this.closeDeletePopup();
                    });
                },
                saveDefaultCertificate(event) {
                    if (event.currentTarget.classList.contains('masterstudy-button_disabled')) {
                        return;
                    }
                    this.loadingSaveButton = true;
                    const data = {
                        new_certificate: this.newDefaultCertificate.id,
                        action: 'stm_save_default_certificate',
                        nonce: stm_lms_nonces['stm_save_default_certificate'],
                    };
                    this.$http.post(stm_lms_ajaxurl, data, {emulateJSON: true}).then(function(response) {
                        if (response.data === "saved") {
                            this.defaultCertificate = this.newDefaultCertificate;
                            this.closeCertificatesPopup();
                        }
                        this.loadingSaveButton = false;
                    });
                },
                updateDefaultCertificate() {
                    const defaultCertExists = this.savedCertificates.some(certificate => certificate.id === this.defaultCertificate.id);
                    if (!defaultCertExists) {
                        this.defaultCertificate = '';
                    }
                },
                activateField(key) {
                    this.activeField = key;
                    if (this.$refs[`shapeField-${key}`] && this.$refs[`shapeField-${key}`][0]) {
                        this.activeFieldShapeElement = this.$refs[`shapeField-${key}`][0];
                    } else {
                        this.activeFieldShapeElement = null;
                    }

                    let field = this.certificates[this.currentCertificate].data.fields[key];
                    if (field) {
                        this.getField = field;
                        this.onFontSelect(field);
                    }
                },
                onDragStart() {
                    this.applyClassToTextFields(true, 'masterstudy-select-none');
                    this.hideShapeSettings();
                },
                onResizeStart() {
                    this.applyClassToTextFields(true, 'masterstudy-select-none');
                    this.hideShapeSettings();
                },
                onResizeStop(left, top, width, height, key) {
                    this.onResize(left, top, width, height);
                    this.onResizeEnd();
                    const currentField = this.certificates[this.currentCertificate].data.fields[key];
                    if (currentField.type === 'shape') {
                        this.shapeSettingsPosition(left, top, key);
                    }
                },
                onDragStop(left, top, key) {
                    this.onDrag(left, top, key);
                    this.onDragEnd();
                    const currentField = this.certificates[this.currentCertificate].data.fields[key];
                    if (currentField.type === 'shape') {
                        this.shapeSettingsPosition(left, top, key);
                    }
                },
                onDragEnd() {
                    this.applyClassToTextFields(false, 'masterstudy-select-none');
                },
                onDrag(left, top, key) {
                    let leftBoundary, rightBoundary;
                    if (typeof this.certificates[this.currentCertificate].data.fields[this.activeField] !== 'undefined') {
                        let classes = '';
                        if (this.certificates[this.currentCertificate].data.orientation === 'landscape') {
                            leftBoundary = 48;
                            rightBoundary = 700;
                        } else {
                            leftBoundary = 50;
                            rightBoundary = 395;
                        }

                        if (left < leftBoundary) {
                            classes = 'left-align';
                        } else if (left > rightBoundary) {
                            classes = 'right-align';
                        }

                        if(top < 100){
                            if (classes.length > 0) {
                                classes += ' ';
                            }
                            classes += 'top-align';
                        }
                        const currentField = this.certificates[this.currentCertificate].data.fields[this.activeField];
                        this.$set(currentField, 'x', left);
                        this.$set(currentField, 'y', top);
                        this.$set(currentField, 'classes', classes);
                        this.colorPickerPosition(left, top);
                        if (currentField.type === 'shape') {
                            this.shapeSettingsPosition(left, top, key);
                        }
                    }
                },
                onResize(left, top, width, height) {
                    if (typeof this.certificates[this.currentCertificate].data.fields[this.activeField] !== 'undefined') {
                        const activeField = this.certificates[this.currentCertificate].data.fields[this.activeField];
                        this.$set(activeField, 'x', left);
                        this.$set(activeField, 'y', top);
                        this.$set(activeField, 'w', width);
                        this.$set(activeField, 'h', height);
                    }
                },
                onResizeEnd() {
                    this.applyClassToTextFields(false, 'masterstudy-select-none');
                },
                applyClassToTextFields(addClass, className) {
                    const elements = document.querySelectorAll('.masterstudy-certificate-canvas-wrap__zone textarea, .masterstudy-certificate-canvas-wrap__zone img');
                    elements.forEach((element) => {
                        if (addClass) {
                            if (!element.classList.contains(className)) {
                                element.classList.add(className);
                            }
                        } else {
                            if (element.classList.contains(className)) {
                                element.classList.remove(className);
                            }
                            if (element.tagName.toLowerCase() === 'textarea' || element.tagName.toLowerCase() === 'input') {
                                element.blur();
                            }
                        }
                    });
                },
                handleFieldClick(left, top) {
                    this.colorPickerPosition(left, top);
                },
                handleFieldShapeClick(left, top, key) {
                    this.colorPickerPosition(left, top);
                    const currentField = this.certificates[this.currentCertificate].data.fields[key];
                    if (currentField && currentField.type === 'shape') {
                        this.shapeSettingsPosition(left, top, key);
                    }
                },
                shapeSettingsPosition(left, top, key) {
                    this.ActiveShapefieldKey = key;
                    this.ActiveShapefield = this.certificates[this.currentCertificate].data.fields[key];
                    if (!this.ActiveShapefield || this.ActiveShapefield.type !== 'shape') {
                        return;
                    }
                    const shapeSettingsElement = document.querySelector('.masterstudy-shape-settings');
                    if (shapeSettingsElement) {
                        const activeFieldWidth = this.ActiveShapefield.w;
                        const shapeSettingsWidth = shapeSettingsElement.offsetWidth;
                        const centeredLeft = left + (activeFieldWidth / 2) - (shapeSettingsWidth / 2);
                        shapeSettingsElement.style.position = 'absolute';
                        shapeSettingsElement.style.transform = `translate(${centeredLeft}px, ${top}px)`;
                        shapeSettingsElement.style.opacity = '1';
                        shapeSettingsElement.style.visibility = 'visible';
                    }
                },
                hideShapeSettings() {
                    const shapeSettingsElement = document.querySelector('.masterstudy-shape-settings');
                    if (shapeSettingsElement) {
                        shapeSettingsElement.style.opacity = '0';
                        shapeSettingsElement.style.visibility = 'hidden';
                        this.ActiveShapefieldKey = null;
                        this.ActiveShapefield = null;
                    }
                },
                handleClickOutsideShapeSettings(event) {
                    const clickedOutsideField = !event.target.closest('.draggable.resizable') && !event.target.closest('.masterstudy-shape-settings');

                    if (!this.activeFieldShapeElement || clickedOutsideField) {
                        this.hideShapeSettings();
                    }
                },
                colorPickerPosition(left, top) {
                    const isLandscape = this.certificates[this.currentCertificate].data.orientation === 'landscape';
                    let fieldWidth = 515,
                    fieldHeight = 310,
                    leftPosition = 0,
                    topPosition = 0,
                    dragAreaWidth, dragAreaHeight, leftCoef, topCoef;
                    if (typeof this.certificates[this.currentCertificate].data.fields[this.activeField] !== 'undefined') {
                        if (isLandscape) {
                            dragAreaWidth = 900;
                            dragAreaHeight = 591;
                            leftCoef = 70;
                            topCoef = 70;
                        } else {
                            fieldWidth = 288
                            fieldHeight = 577
                            dragAreaWidth = 591;
                            dragAreaHeight = 900;
                            leftCoef = 70;
                            topCoef = 50;
                        }

                        if (left + fieldWidth > dragAreaWidth) {
                            leftPosition = dragAreaWidth - fieldWidth - leftCoef;
                        }

                        if (top + fieldHeight > dragAreaHeight) {
                            topPosition = dragAreaHeight - fieldHeight - topCoef;
                        }

                        if (!isLandscape && top > 550) {
                            topPosition = 500;
                        }

                        const colorPicker = document.querySelector('.active.draggable.resizable .field-content .vc-photoshop');
                        if ( colorPicker ) {
                            colorPicker.style.top = `-${topPosition}px`;
                            colorPicker.style.left = `-${leftPosition}px`;
                        }
                    }
                },
                toggleShapeDropdown() {
                    this.dropdownShapeOpen = !this.dropdownShapeOpen;
                    this.colorPickerVisible = false;
                    if (this.dropdownShapeOpen) {
                        document.addEventListener('click', this.handleClickOutsideShape);
                    } else {
                        document.removeEventListener('click', this.handleClickOutsideShape);
                    }
                },
                handleClickOutsideShape(event) {
                    const dropdown = this.$el.querySelector('.masterstudy-shape-dropdown');
                    if (dropdown && !dropdown.contains(event.target)) {
                      this.dropdownShapeOpen = false;
                      document.removeEventListener('click', this.handleClickOutsideShape);
                    }
                },
                getShapeById(shapeId) {
                    return this.shapes.find(shape => shape.id === shapeId);
                },
                selectShape(shapeId, index) {
                    this.$set(this.certificates[this.currentCertificate].data.fields[index], 'content', shapeId);
                    this.dropdownShapeOpen = false;
                    document.removeEventListener('click', this.handleClickOutsideShape);
                },
            }
        });
    });
})(jQuery);
