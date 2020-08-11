function csvData() {
    return {
        active: 'parse-section',
        fileName: '',
        header: [],
        rows: [],
        dataList: [],
        emailCol: 'null',
        subject: null,
        message: null,
        isLoading: false,
        isShowColMenu: false,
        isMappingDone: false,
        selectedTplKey: "null",
        selectedTpl: {},
        templates: [
            {
                name: 'free-ebook',
                templates: {
                    subject: `{{#if firstName}}{{firstName}} - our lucky friend!{{else}}Our lucky friend!{{/if}}`,
                    message: `Hello {{firstName}}, we have exciting news for you! We are sending free ebook to your email {{email}}.`
                },
                requiredVars: [{key: 'firstName', label: 'First Name'}, {key: 'email', label: 'Email'}],
                mappedVars: {firstName: 'null', email: this.emailCol}
            },
            {
                name: 'new-product',
                templates: {
                    subject: `{{#if firstName}}{{firstName}} - our lucky friend!{{else}}Our lucky friend!{{/if}}`,
                    message: `Hello {{firstName}}, we have exciting news for you! We are sending free ebook of our new product to your email {{email}}.`
                },
                requiredVars: [{key: 'firstName', label: 'First Name'}, {key: 'email', label: 'Email'}],
                mappedVars: {firstName: 'null', email: this.emailCol}
            }
        ],
        async parse() {
            var fileUpload = document.getElementById("fileUpload");
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv|.txt)$/;
            if (regex.test(fileUpload.value.toLowerCase())) {
                this.rows = await this.paparse(fileUpload.files[0], { skipEmptyLines: true });
                this.rows.pop();
                this.header = Object.keys(this.rows[0]);
                this.parseEmail(this.rows[0]);
            } else {
                alert("Please upload a valid CSV file.");
            }
        },
        paparse(file) {
            return new Promise((resolve, reject) => {
                Papa.parse(file, {
                  header: true,
                  complete (results) {
                    resolve(results.data)
                  },    
                  error (err) {
                    reject(err)
                  }
                })
            })
        },
        generate() {
            if (!(this.subject && this.message)) {
                alert("Subject and message templates must not be empty.");
            }

            const subjectTpl = Handlebars.compile(this.subject);
            const messageTpl = Handlebars.compile(this.message);
            this.dataList = this.rows.reduce((dRows, row, index) => {
                const newRow = row;
                newRow['subject'] = subjectTpl(row);
                newRow['message'] = messageTpl(row);
                dRows.push(row);
                return dRows;
            }, []);
            this.active = 'table-section'
        },
        parseEmail(row)
        {
            for (const key in row) {
                if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(row[key])) {
                    this.emailCol = key;
                    break;
                }
            }
            if (!this.emailCol) {
                this.active = 'parse-section';
                this.fileName = '';
                alert('No email column detected! Make sure your csv has email column.');
            }
            else {
                this.active = 'generate-section';
            }
        },
        backToParse() {
            this.active = 'parse-section'; 
            this.fileName = '';
        },
        updateTpl() {
            this.selectedTpl = this.templates.find(tpl => tpl.name === this.selectedTplKey);
            this.subject = this.selectedTpl.templates.subject;
            this.message = this.selectedTpl.templates.message;
            this.selectedTpl.mappedVars.email = this.emailCol
        },
        mappingDone() {
            this.updateTpl();
            const mappedVars = {};
            for (const key in this.selectedTpl.mappedVars) {
                if (this.selectedTpl.mappedVars[key] !== 'null') {
                    mappedVars[key] = this.selectedTpl.mappedVars[key];
                }
            }

            const re = new RegExp(Object.keys(mappedVars).join("|"),"gi");
            this.subject = this.subject.replace(re, function(matched){
                return mappedVars[matched];
            });
            this.message = this.message.replace(re, function(matched){
                return mappedVars[matched];
            });
        },
        save() {
            let url = '/setup/campaign';
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            this.isLoading = true;
            fetch(url, {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                        },
                    method: 'post',
                    credentials: "same-origin",
                    body: JSON.stringify({
                        contacts: this.rows,
                        templates: this.templates,
                        data: this.dataList,
                        emailCol: this.emailCol,
                        campaingName: this.selectedTpl.name
                    })
                })
                .then((data) => {
                    this.isLoading = false;
                    this.active = 'finished-section';
                })
                .catch(function(error) {
                    //console.log(error);
                });
        }
    }
}