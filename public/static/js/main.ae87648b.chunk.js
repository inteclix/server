(this.webpackJsonpfrontend2=this.webpackJsonpfrontend2||[]).push([[0],{292:function(e,t,a){},470:function(e,t,a){"use strict";a.r(t);var l=a(6),n=a(0),i=a(42),r=a.n(i),c=a(8),s=a(253),u=a.n(s),o=(a(290),a(291),a(292),a(41)),d=a(103),b=a(54),O=Object(n.createContext)(null);function j(e){var t=e.children,a=e.value;return Object(l.jsx)(O.Provider,{value:Object(b.a)({},a),children:t})}for(var h=function(){return Object(n.useContext)(O)},p=a(50),m=a(66),E=a(474),f=a(118),A=a(48),v=a(78),I=a(49),T=a(481),x=a(482),S=a(483),R=function(){return Object(l.jsx)("div",{children:"notification"})},N=function(){var e=h(),t=e.user,a=e.setToken,n=e.setUser;function i(e){"1"===e.key&&(a(null),n(null))}return Object(l.jsxs)(E.a.Header,{style:{height:48,backgroundColor:"white",position:"fixed",zIndex:2,width:"calc(100% - 200px)",boxShadow:"0 1px 4px rgba(0,21,41,.08)",display:"flex",justifyContent:"flex-end",alignItems:"center"},children:[Object(l.jsx)(f.a,{trigger:"click",title:"Notifications",content:R,children:Object(l.jsx)(A.a,{icon:Object(l.jsx)(T.a,{}),style:{marginRight:10},children:5})}),Object(l.jsx)(v.a,{trigger:"click",placement:"bottomRight",overlay:function(){return Object(l.jsx)(I.a,{children:Object(l.jsx)(I.a.Item,{onClick:i,icon:Object(l.jsx)(x.a,{style:{color:"red"}}),children:"Se d\xe9connecter"},"1")})},children:Object(l.jsxs)(A.a,{children:[t.username," ",Object(l.jsx)(S.a,{})]})})]})},C=a(487),g=a(488),y=a(478),L=a(484),U="Champ obligatoire",H=[{value:"CONDOR LOGISTICS",label:"CONDOR LOGISTICS"},{value:"CONDOR ELECTRONICS",label:"CONDOR ELECTRONICS"},{value:"TRAVOCOVIA",label:"TRAVOCOVIA"}],V=function(e){var t=e.title,a=e.withBack,i=e.loading,r=e.selectedSiderKey,c=e.children,s=Object(p.f)(),u=h().setSelectedSiderKey;return Object(n.useEffect)((function(){u(r||"")}),[]),Object(n.useEffect)((function(){document.title=t?t+" - "+de.appName:de.appName}),[t]),i&&a?Object(l.jsxs)("div",{style:{padding:15},children:[Object(l.jsx)(A.a,{icon:Object(l.jsx)(L.a,{}),onClick:function(){return s.goBack()}}),Object(l.jsx)("hr",{}),Object(l.jsx)(y.a,{})]}):i?Object(l.jsx)("div",{style:{padding:15},children:Object(l.jsx)(y.a,{})}):Object(l.jsxs)("div",{style:{padding:15},children:[a&&Object(l.jsxs)(l.Fragment,{children:[Object(l.jsxs)("div",{style:{display:"flex",alignItems:"baseline"},children:[Object(l.jsx)(A.a,{icon:Object(l.jsx)(L.a,{}),onClick:function(){return s.goBack()}}),Object(l.jsx)("h1",{style:{marginLeft:20},children:t})]}),Object(l.jsx)("hr",{})]}),!a&&Object(l.jsxs)(l.Fragment,{children:[Object(l.jsx)("h1",{children:t}),Object(l.jsx)("hr",{})]}),c]})},D=a(475),k=function(e){var t=e.url,a=e.mapOptionToString,i=e.placeholder,r=e.onSelect,c=e.onChange,s=e.style,u=e.defaultOption,d=h().api,O=Object(n.useState)(""),j=Object(o.a)(O,2),p=(j[0],j[1],Object(n.useState)([])),m=Object(o.a)(p,2),E=m[0],f=m[1];return Object(l.jsx)(D.a,{options:E,defaultValue:function(){u&&a(u)},style:s,onSelect:r,onSearch:function(e){d.get("".concat(t,"?data=").concat(e)).then((function(e){var t=e.data.data.map((function(e){return Object(b.a)(Object(b.a)({},e),{},{value:a(e)})}));f(t)}))},onChange:c,placeholder:i})},G=(a(44),a(131)),M=a(104),P=a(72),B=a(63),K=a(56),Y=a(68),F=a(83),_=a(194),q=(a(448),a(173)),w=a(485),X=a(486),W=function(e){var t=e.defaultValue,a=e.onChange,i=Object(n.useState)(0),r=Object(o.a)(i,2),c=r[0],s=r[1];Object(n.useEffect)((function(){t&&s(t)}),[]),Object(n.useEffect)((function(){a(c)}),[c]);return Object(l.jsxs)("div",{style:{display:"flex",flexDirection:"column",justifyContent:"center",alignItems:"center"},children:[Object(l.jsx)(q.a,{type:"circle",percent:c}),Object(l.jsxs)(A.a.Group,{style:{marginTop:5},children:[Object(l.jsx)(A.a,{onClick:function(){var e=c-10;e<0&&(e=0),s(e)},icon:Object(l.jsx)(w.a,{})}),Object(l.jsx)(A.a,{onClick:function(){var e=c+10;e>100&&(e=100),s(e)},icon:Object(l.jsx)(X.a,{})})]})]})},z=P.a.Option,Z=B.a.TextArea,J=function(e){var t=e.formItems,a=e.initialValues,n=void 0===a?[]:a,i=e.onFinish,r=e.onFinishFailed,c=K.a.useForm(),s=Object(o.a)(c,1)[0];return Object(l.jsxs)(K.a,Object(b.a)(Object(b.a)({},{labelCol:{xs:{span:24},sm:{span:5}},wrapperCol:{xs:{span:24},sm:{span:12}}}),{},{name:"basic",form:s,layout:"vertical",initialValues:n,onFinish:i,onFinishFailed:r,children:[t.map((function(e,t){if("progress"===e.type)return Object(l.jsx)(K.a.Item,{name:e.name,label:e.label,hasFeedback:!0,rules:e.rules,children:Object(l.jsx)(W,{defaultValue:n[e.name],onChange:function(t){s.setFieldsValue(e.name,t)}})});if("search"===e.type){e.renderValue;return Object(l.jsx)(K.a.Item,{name:e.name,label:e.label,hasFeedback:!0,rules:e.rules,children:Object(l.jsx)(k,{url:e.url,defaultOption:n[e.defaultOptionName],mapOptionToString:e.mapOptionToString,onSelect:function(t,a){var l={};l[e.name]=a.id,s.setFieldsValue(l)},onChange:function(t,a){if(!a){var l={};l[e.name]=null,s.setFieldsValue(l)}},placeholder:e.label})})}return"select"===e.type?Object(l.jsx)(K.a.Item,{name:e.name,label:e.label,hasFeedback:!0,initialValue:e.initialValue,rules:e.rules,children:Object(l.jsx)(P.a,{placeholder:"Selection\xe9",children:e.selects.map((function(e,t){return Object(l.jsx)(z,{value:e.value,children:e.label},t)}))})}):"text"===e.type?Object(l.jsx)(K.a.Item,{label:e.label,name:e.name,rules:e.rules,children:Object(l.jsx)(B.a,Object(b.a)({},e.inputProp))}):"textarea"===e.type?Object(l.jsx)(K.a.Item,{label:e.label,name:e.name,rules:e.rules,children:Object(l.jsx)(Z,{})}):"password"===e.type?Object(l.jsx)(K.a.Item,{label:e.label,name:e.name,rules:e.rules,children:Object(l.jsx)(B.a.Password,{})}):"checkbox"===e.type?Object(l.jsx)(K.a.Item,{name:e.name,valuePropName:"checked",children:Object(l.jsx)(Y.a,{children:e.label})}):"date"===e.type?Object(l.jsx)(K.a.Item,{label:e.label,name:e.name,valuePropName:"checked",children:Object(l.jsx)(F.a,{style:{width:"100%"}})}):"rate"===e.type?Object(l.jsx)(K.a.Item,{label:e.label,name:e.name,children:Object(l.jsx)(_.a,{style:{backgroundColor:"white"}})}):"integer"===e.type?Object(l.jsx)(K.a.Item,{label:e.label,name:e.name,children:Object(l.jsx)(B.a,Object(b.a)({type:"number",pattern:"[0-9]*"},e.inputProp))}):void 0})),Object(l.jsx)(K.a.Item,Object(b.a)(Object(b.a)({},{}),{},{children:Object(l.jsx)(A.a,{type:"primary",htmlType:"submit",children:"Envoyer"})}))]}))},Q=[{name:"username",label:"Nom d'utilisateur",rules:[{required:!0,message:U}],type:"text"},{name:"firstname",label:"Nom",rules:[{required:!0,message:U}],type:"text"},{name:"lastname",label:"Prenom",rules:[{required:!0,message:U}],type:"text"},{name:"mail",label:"Email",rules:[{required:!0,message:U}],type:"text"},{name:"password",label:"Mot de pass",rules:[{required:!0,message:U}],type:"password"},{name:"poste",label:"Poste",rules:[{required:!0,message:U}],type:"select",selects:[{value:"admin",label:"Administarateur"},{value:"ga",label:"Gestionnaire administaratif"},{value:"gm",label:"Gestionnaire mat\xe9riel"},{value:"com",label:"Commercial"}]},{name:"tel",label:"TEL",rules:[{required:!0,message:U}],type:"text"}],$=[{name:"matricule",label:"Matricule",rules:[{required:!0,message:U}],type:"text"},{name:"prop",label:"prop",rules:[{required:!0,message:U}],type:"select",selects:H},{name:"old_matricule",label:"old_matricule",rules:[{required:!0,message:U}],type:"text"},{name:"code_gps",label:"code_gps",rules:[{required:!0,message:U}],type:"text"},{name:"genre",label:"genre",rules:[{required:!0,message:U}],type:"select",selects:[{label:"V P",value:"V P"},{label:"CAMION",value:"CAMION"},{label:"TRACTEUR",value:"TRACTEUR"},{label:"BUS",value:"BUS"},{label:"UTILITAIRE",value:"UTILITAIRE"},{label:"MICRO BUS",value:"MICRO BUS"},{label:"V H",value:"V H"},{label:"MOTO",value:"MOTO"},{label:"AMBULANCE",value:"AMBULANCE"},{label:"MOTOCYCLE",value:"MOTOCYCLE"},{label:"RETRO CHARGEUR",value:"RETRO CHARGEUR"},{label:"CHARIOT ELEVATEUR",value:"CHARIOT ELEVATEUR"},{label:"CHARIOT ELEVATEUR  1.6T-MAL",value:"CHARIOT ELEVATEUR  1.6T-MAL"},{label:"CHARIOT ELEVATEUR  2.5T",value:"CHARIOT ELEVATEUR  2.5T"},{label:"CHARIOT ELEVATEUR  2T-MAL",value:"CHARIOT ELEVATEUR  2T-MAL"},{label:"CHARIOT ELEVATEUR 03T",value:"CHARIOT ELEVATEUR 03T"},{label:"CHARIOT ELEVATEUR 5T-MAL",value:"CHARIOT ELEVATEUR 5T-MAL"},{label:"CHARIOT ELEVATEUR -MAL",value:"CHARIOT ELEVATEUR -MAL"},{label:"EXCAVATRICE",value:"EXCAVATRICE"},{label:"STAKAR",value:"STAKAR"},{label:"STATION D'EPURATION",value:"STATION D'EPURATION"},{label:"TANDEM VIBRATOIR",value:"TANDEM VIBRATOIR"},{label:"TRANSPALETTE ELECTRIQUE  1.6T ",value:"TRANSPALETTE ELECTRIQUE  1.6T "},{label:"TRANSPALETTE GERBEUR  1.4T-MAL",value:"TRANSPALETTE GERBEUR  1.4T-MAL"},{label:"",value:""}]},{name:"marque",label:"marque",rules:[{required:!0,message:U}],type:"select",selects:[{label:"RENAULT CLIO",value:"RENAULT CLIO"},{label:"DACIA SENDERO",value:"DACIA SENDERO"},{label:"CHEVROLET AVIO",value:"CHEVROLET AVIO"},{label:"CHEVROLET BREAK",value:"CHEVROLET BREAK"},{label:"OPEL",value:"OPEL"},{label:"CHEVROLET SAIL",value:"CHEVROLET SAIL"},{label:"RENAULT SYMBOL",value:"RENAULT SYMBOL"},{label:"FOTON",value:"FOTON"},{label:"DACIA LOGAN",value:"DACIA LOGAN"},{label:"DFSK",value:"DFSK"},{label:"CHEVROLET CRUZE",value:"CHEVROLET CRUZE"},{label:"PEUGEOT PARTNER",value:"PEUGEOT PARTNER"},{label:"SUSUKI SWIFT",value:"SUSUKI SWIFT"},{label:"CHEVROLET TRAX",value:"CHEVROLET TRAX"},{label:"CHEVROLET AVIO ",value:"CHEVROLET AVIO "},{label:"PEUGEOT 207",value:"PEUGEOT 207"},{label:"CHANGHE",value:"CHANGHE"},{label:"NISSAN SUNNY",value:"NISSAN SUNNY"},{label:"HONDA ODYSSEY",value:"HONDA ODYSSEY"},{label:"MITSUBISHI MIRAGE STAR",value:"MITSUBISHI MIRAGE STAR"},{label:"NISSAN NEW SUNNY ",value:"NISSAN NEW SUNNY "},{label:"HYUNDAI VERNA",value:"HYUNDAI VERNA"},{label:"MERCEDES-Benz",value:"MERCEDES-Benz"},{label:"CHEVROLET SPARK",value:"CHEVROLET SPARK"},{label:"KIA PICANTO",value:"KIA PICANTO"},{label:"TOYOTA COROLLA",value:"TOYOTA COROLLA"},{label:"BENTLIY",value:"BENTLIY"},{label:"PAOLETTI RACING",value:"PAOLETTI RACING"},{label:"CHEVROLET SONIC",value:"CHEVROLET SONIC"},{label:"TOYOTA YARIS",value:"TOYOTA YARIS"},{label:"AUDI",value:"AUDI"},{label:"HYUNDAI GENESIS",value:"HYUNDAI GENESIS"},{label:"KIA SPORTAGE",value:"KIA SPORTAGE"},{label:"PEUGEOT 301",value:"PEUGEOT 301"},{label:"SEAT / IBIZA",value:"SEAT / IBIZA"},{label:"VOLKSWAGEN POLO",value:"VOLKSWAGEN POLO"},{label:"CITROEN CI-ELYSSE",value:"CITROEN CI-ELYSSE"},{label:"VMS",value:"VMS"},{label:"SKODA RAPIDE",value:"SKODA RAPIDE"},{label:"RENAULT FLUENCE",value:"RENAULT FLUENCE"},{label:"RENAULT GLR",value:"RENAULT GLR"},{label:"RENAULT",value:"RENAULT"},{label:"VOLKWAGEN",value:"VOLKWAGEN"},{label:"IVECO",value:"IVECO"},{label:"MERCEDES ACTROS",value:"MERCEDES ACTROS"},{label:"DAIHATSU",value:"DAIHATSU"},{label:"NISSAN PIK-UP",value:"NISSAN PIK-UP"},{label:"DAIMER,BENZ",value:"DAIMER,BENZ"},{label:"ISUZU",value:"ISUZU"},{label:"RENAULT KANGO",value:"RENAULT KANGO"},{label:"FAW",value:"FAW"},{label:"CHENG LONG",value:"CHENG LONG"},{label:"DONGFENG",value:"DONGFENG"},{label:"HYUNDAI HD17",value:"HYUNDAI HD17"},{label:"HENG TONG",value:"HENG TONG"},{label:"KIA",value:"KIA"},{label:"KIA 2700",value:"KIA 2700"},{label:"PEUGEOT EXPERT",value:"PEUGEOT EXPERT"},{label:"MERCEDES AXOR",value:"MERCEDES AXOR"},{label:"MITSUBISHI",value:"MITSUBISHI"},{label:"NISSAN",value:"NISSAN"},{label:"NISSAN TIDA",value:"NISSAN TIDA"},{label:"RENAULT MEGANE",value:"RENAULT MEGANE"},{label:"JAC",value:"JAC"},{label:"HYUNDAI HD 65",value:"HYUNDAI HD 65"},{label:"TOYOTA HIACE",value:"TOYOTA HIACE"},{label:"YOUNGMAN",value:"YOUNGMAN"},{label:"TOYOTA HILUX",value:"TOYOTA HILUX"},{label:"TOYOTA",value:"TOYOTA"},{label:"SSANG YONG",value:"SSANG YONG"},{label:"CITROEN BERLINGO",value:"CITROEN BERLINGO"},{label:"CHEVROLET COLORADO",value:"CHEVROLET COLORADO"},{label:"PEUGEOT BOXER",value:"PEUGEOT BOXER"},{label:"SKODA OCTAVIA",value:"SKODA OCTAVIA"},{label:"YUEJIN",value:"YUEJIN"},{label:"DFAC",value:"DFAC"},{label:"SHAANQI",value:"SHAANQI"},{label:"RENAULT MASTER",value:"RENAULT MASTER"},{label:"HYUNDAI H 100",value:"HYUNDAI H 100"},{label:"JINBEI",value:"JINBEI"},{label:"TATA",value:"TATA"},{label:"KIA ",value:"KIA "},{label:"HIGER",value:"HIGER"},{label:"MITSUBISHI FUSO",value:"MITSUBISHI FUSO"},{label:"CITROEN CI-ELYSEE",value:"CITROEN CI-ELYSEE"},{label:"HYUNDAI HD 160",value:"HYUNDAI HD 160"},{label:"HINO",value:"HINO"},{label:"TOYOTA COASTER ",value:"TOYOTA COASTER "},{label:"TOYOTA RAV4",value:"TOYOTA RAV4"},{label:"MERCEDES-BENZ GLC 250",value:"MERCEDES-BENZ GLC 250"},{label:"TOYOTA HILUX REVO",value:"TOYOTA HILUX REVO"},{label:"NEW INTERNATIONAL PAYSTAR",value:"NEW INTERNATIONAL PAYSTAR"},{label:"SCANIA",value:"SCANIA"},{label:"NISSAN NAVARA",value:"NISSAN NAVARA"},{label:"MERCEDES",value:"MERCEDES"},{label:"RANGE ROVER",value:"RANGE ROVER"},{label:"HYUNDAI HD 35",value:"HYUNDAI HD 35"},{label:"PEUGEOT 2008",value:"PEUGEOT 2008"},{label:"SKODA YETI",value:"SKODA YETI"},{label:"KIA RIO",value:"KIA RIO"}]},{name:"type",label:"type",rules:[{required:!0,message:U}],type:"text"},{name:"puissance",label:"puissance",rules:[{required:!0,message:U}],type:"text"},{name:"energie",label:"energie",rules:[{required:!0,message:U}],type:"text"},{name:"carrosserie",label:"carrosserie",rules:[{required:!0,message:U}],type:"text"},{name:"color",label:"color",type:"text"}],ee=[{name:"firstname",label:"Nom",rules:[{required:!0,message:U}],type:"text"},{name:"lastname",label:"Prenom",rules:[{required:!0,message:U}],type:"text"},{name:"tel",label:"tel",rules:[{required:!0,message:U}],type:"text"},{name:"code_paie",label:"Code paie",rules:[{required:!0,message:U}],type:"text"},{name:"type",label:"Type",rules:[{required:!0,message:U}],type:"select",selects:[{label:"Chauffeur SR ",value:"Chauffeur SR "},{label:"Chauffeur TC Bus",value:"Chauffeur TC Bus"},{label:"Chauffeur  VL ",value:"Chauffeur  VL "},{label:"Chauffeur  PL",value:"Chauffeur  PL"},{label:"Chauffeur Ambulancier",value:"Chauffeur Ambulancier"}]}],te=function(){var e=h().api,t=Object(p.f)(),a=[{title:"Code (SAP)",dataIndex:"code",copyable:!0,sorter:!0},{title:"Designation",dataIndex:"designation",sorter:!0,hideInSearch:!0},{title:"Localit\xe9",dataIndex:"localite",sorter:!0,hideInSearch:!0},{title:"TEL",dataIndex:"tel",sorter:!0,hideInSearch:!0,copyable:!0},{title:"Client Hierachique",dataIndex:["client","designation"],sorter:!0,hideInSearch:!0},{title:"option",valueType:"option",dataIndex:"id",render:function(a,n,i,r){return[Object(l.jsx)("a",{onClick:function(){e.delete("/clients/"+n.id).then((function(){G.b.info("Bien Supprim\xe9"),r.reload()}))},children:"Supprimer"}),Object(l.jsx)("a",{onClick:function(){t.push("/clients/edit/"+n.id)},children:"Modifier"})]}}],i=n.useRef();return window.actionRef=i,Object(l.jsx)(V,{title:"Clients",selectedSiderKey:"list-clients",children:Object(l.jsx)(M.a,{actionRef:i,size:"small",search:!0,columns:a,ellipsis:!0,request:function(t,a,l){return console.log(a),a&&(t.sortBy=Object.keys(a)[0],t.sort=Object.values(a)[0]),e.get("/clients",{params:t,sort:a}).then((function(e){return e.data}))},pagination:{defaultCurrent:1}})})},ae=[{name:"code",label:"Code (SAP)",rules:[{required:!0,message:U}],type:"text"},{name:"designation",label:"Designation",type:"text"},{name:"localite",label:"Localit\xe9",type:"text"},{name:"tel",label:"TEL",type:"text"},{name:"client_id",label:"Client",type:"search",url:"clients/search",defaultOptionName:"client",mapOptionToString:function(e){return null===e||void 0===e?void 0:e.designation}}],le=a(480),ne=[{name:"client_id",label:"Client",type:"search",rules:[{required:!0,message:U}],url:"clients/search",defaultOptionName:"client",mapOptionToString:function(e){return null===e||void 0===e?void 0:e.designation}},{name:"car_id",label:"V\xe9hicule",type:"search",rules:[{required:!0,message:U}],url:"cars/search",defaultOptionName:"car",mapOptionToString:function(e){return null===e||void 0===e?void 0:e.matricule}},{name:"date_decharge",label:"Date d\xe9charge",rules:[{required:!0,message:U}],type:"date"},{name:"date_fin_prestation",label:"Date fin prestation",type:"date"},{name:"observation",label:"Observation",type:"textarea"}],ie=[{name:"driver_id",label:"Conducteur",type:"search",rules:[{required:!0,message:U}],url:"drivers/search",defaultOptionName:"driver",mapOptionToString:function(e){return(null===e||void 0===e?void 0:e.firstname)+" "+e.lastname}},{name:"niveau_carburant",label:"Niveau carburant",rules:[{required:!0,message:U}],type:"progress"},{name:"odometre",label:"Odometre",rules:[{required:!0,message:U}],type:"integer",inputProp:{suffix:"KM"}},{name:"starts",label:"Etat de v\xe9hicule",type:"rate"},{name:"cle_vehicule",label:"Nbs cl\xe9s v\xe9hicule",type:"integer",inputProp:{suffix:"Cl\xe9(s)"}},{name:"carte_grise",label:"Carte grise",type:"checkbox"},{name:"assurance",label:"assurance",type:"checkbox"},{name:"scanner",label:"scanner",type:"checkbox"},{name:"permis_circuler",label:"permis_circuler",type:"checkbox"},{name:"carnet_enter",label:"carnet_enter",type:"checkbox"},{name:"vignette",label:"vignette",type:"checkbox"},{name:"carte_gpl",label:"carte_gpl",type:"checkbox"},{name:"gillet",label:"gillet",type:"checkbox"},{name:"roue_secour",label:"roue_secour",type:"checkbox"},{name:"cric",label:"cric",type:"checkbox"},{name:"poste_radio",label:"poste_radio",type:"checkbox"},{name:"cle_roue",label:"cle_roue",type:"checkbox"},{name:"extincteur",label:"extincteur",type:"checkbox"},{name:"boite_pharm",label:"boite_pharm",type:"checkbox"},{name:"triangle",label:"triangle",type:"checkbox"},{name:"pochette_cle",label:"pochette_cle",type:"checkbox"},{name:"observation",label:"observation",type:"textarea"}],re=le.a.Step,ce=a(195),se=a(479),ue=[],oe=0;oe<20;oe++)ue.push({key:oe.toString(),matricule:"content".concat(oe+1),description:"description of content".concat(oe+1)});var de={appName:"GestPark v1.0.0",routes:[{path:"/dashboard",name:"dashboard",label:"Dashboard",component:function(){return Object(l.jsx)(V,{children:Object(l.jsx)(k,{url:"drivers/search",style:{width:200},mapOptionToString:function(e){return e.firstname+" "+e.lastname},onSelect:function(e,t){console.log(t),console.log(e)},placeholder:"Vehicule"})})},icon:Object(l.jsx)(C.a,{}),authority:["dashboard"]},{name:"users",label:"Utilisateurs",icon:Object(l.jsx)(g.a,{}),routes:[{path:"/users",name:"list-users",label:"List des utilisateurs",component:function(){var e=h().api,t=Object(p.f)(),a=[{title:"Nom d'utilisateur",dataIndex:"username",copyable:!0,filters:!0,sorter:!0},{title:"Nom",dataIndex:"firstname",sorter:!0,hideInSearch:!0},{title:"Pr\xe9nom",dataIndex:"lastname",sorter:!0,hideInSearch:!0},{title:"Poste",dataIndex:"poste",sorter:!0,hideInSearch:!0},{title:"Email",dataIndex:"mail",valueType:"email",sorter:!0,hideInSearch:!0},{title:"Date Cre\xe9ation",dataIndex:"created_at",valueType:"dateTime",hideInSearch:!0},{title:"Tel",dataIndex:"tel",copyable:!0,sorter:!0,hideInSearch:!0},{title:"option",valueType:"option",dataIndex:"id",render:function(a,n,i,r){return[Object(l.jsx)("a",{onClick:function(){e.delete("auth/users/"+n.id).then((function(){G.b.info("Bien Supprim\xe9"),r.reload()}))},children:"Supprimer"}),Object(l.jsx)("a",{onClick:function(){t.push("/users/edit/"+n.id)},children:"Modifier"})]}}],i=n.useRef();return window.actionRef=i,Object(l.jsx)(V,{title:"Utilisateurs",selectedSiderKey:"list-users",children:Object(l.jsx)(M.a,{actionRef:i,size:"small",search:!0,columns:a,ellipsis:!0,request:function(t,a,l){return console.log(a),a&&(t.sortBy=Object.keys(a)[0],t.sort=Object.values(a)[0]),e.get("/auth/users",{params:t,sort:a}).then((function(e){return e.data}))},pagination:{defaultCurrent:1}})})},exact:!0},{path:"/users/add",name:"add-user",label:"Nouveau utilisateur",component:function(){var e=h().api;return Object(l.jsx)(V,{title:"Nauveau Utilisateur",selectedSiderKey:"add-users",children:Object(l.jsx)(J,{formItems:Q,onFinish:function(t){e.post("/auth/users",t).then((function(e){G.b.info("Bien ajouter")}))}})})}},{path:"/users/edit/:id",name:"edit-user",label:"Modifier l'utilisateur",component:function(){var e=Object(p.g)().params,t=h().api,a=Object(n.useState)(!0),i=Object(o.a)(a,2),r=i[0],c=i[1],s=Object(n.useState)({}),u=Object(o.a)(s,2),d=u[0],b=u[1];return Object(n.useEffect)((function(){t.get("/auth/users/"+e.id).then((function(e){var t=e.data;b(t.data),c(!1)}))}),[]),Object(l.jsx)(V,{withBack:!0,loading:r,title:"Modifie l'utilisateur",selectedSiderKey:"list-users",children:Object(l.jsx)(J,{formItems:Q,initialValues:d,onFinish:function(a){t.put("/auth/users/"+e.id,a).then((function(e){G.b.info("Bien ajouter")}))}})})},hideInSide:!0},{path:"/users/cars",name:"user-cars-users",label:"Affect\xe9 les v\xe9hicule au utilisateurs",component:function(){var e=h().api,t=Object(n.useState)([]),a=Object(o.a)(t,2),i=a[0],r=a[1],c=Object(n.useState)("vh"),s=Object(o.a)(c,2),u=s[0],d=(s[1],Object(n.useState)({})),O=Object(o.a)(d,2),j=(O[0],O[1]),p=Object(n.useState)([]),m=Object(o.a)(p,2),E=m[0],f=m[1],A=Object(n.useState)([]),v=Object(o.a)(A,2),I=v[0],T=v[1];Object(n.useEffect)((function(){e.get("allcars").then((function(e){var t=e.data.data.map((function(e){return Object(b.a)(Object(b.a)({},e),{},{key:e.id})}));r(t)}))}),[u]);return Object(l.jsxs)(V,{title:"Affect\xe9 des vehicules au utilisateur",selectedSiderKey:"user-cars-users",children:[Object(l.jsx)(k,{url:"auth/users/search",style:{width:200},mapOptionToString:function(e){return e.firstname+" "+e.lastname},onSelect:function(e,t){j({user_id:t.id})},onChange:function(e,t){j(t?{user_id:t.id}:{user_id:null})},placeholder:"Utilisateur"}),Object(l.jsx)("hr",{}),Object(l.jsx)(se.a,{dataSource:i,titles:["Tout","Affect\xe9"],targetKeys:I,selectedKeys:E,onChange:function(e,t,a){T(e),console.log("targetKeys: ",e),console.log("direction: ",t),console.log("moveKeys: ",a)},onSelectChange:function(e,t){f([].concat(Object(ce.a)(e),Object(ce.a)(t))),console.log("sourceSelectedKeys: ",e),console.log("targetSelectedKeys: ",t)},render:function(e){return e.code_gps+" | "+e.matricule},style:{marginBottom:16},listStyle:{width:250,height:250},disabled:!1})]})},exact:!0}]},{name:"cars",label:"V\xe9hicules",icon:Object(l.jsx)(g.a,{}),routes:[{path:"/cars",name:"list-cars",label:"List des v\xe9hicules",component:function(){var e=h().api,t=Object(p.f)(),a=[{title:"matricule",dataIndex:"matricule",copyable:!0,sorter:!0},{title:"prop",dataIndex:"prop",sorter:!0,filters:H.map((function(e){return{text:e.label,value:e.value}})),hideInSearch:!0},{title:"old_matricule",dataIndex:"old_matricule",sorter:!0,hideInSearch:!0},{title:"code_gps",dataIndex:"code_gps",sorter:!0,hideInSearch:!0},{title:"genre",dataIndex:"genre",sorter:!0,hideInSearch:!0},{title:"marque",dataIndex:"marque",sorter:!0,hideInSearch:!0},{title:"carrosserie",dataIndex:"carrosserie",sorter:!0,hideInSearch:!0},{title:"color",dataIndex:"color",sorter:!0,hideInSearch:!0},{title:"option",valueType:"option",dataIndex:"id",render:function(a,n,i,r){return[Object(l.jsx)("a",{onClick:function(){e.delete("/cars/"+n.id).then((function(){G.b.info("Bien Supprim\xe9"),r.reload()}))},children:"Supprimer"}),Object(l.jsx)("a",{onClick:function(){t.push("/cars/edit/"+n.id)},children:"Modifier"})]}}],i=n.useRef();return window.actionRef=i,Object(l.jsx)(V,{title:"V\xe9hicules",selectedSiderKey:"list-cars",children:Object(l.jsx)(M.a,{actionRef:i,size:"small",search:!0,columns:a,ellipsis:!0,request:function(t,a,l){return a&&(t.sortBy=Object.keys(a)[0],t.sort=Object.values(a)[0]),l&&(t.filterBy=Object.keys(l)[0],t.filter=Object.values(l)),e.get("/cars",{params:t}).then((function(e){return e.data}))},pagination:{defaultCurrent:1,pageSize:10}})})},exact:!0},{path:"/cars/add",name:"add-cars",label:"Nauveau v\xe9hicule",component:function(){var e=h().api;return Object(l.jsx)(V,{title:"Nauveau v\xe9hicule",selectedSiderKey:"add-cars",children:Object(l.jsx)(J,{formItems:$,onFinish:function(t){e.post("/auth/users",t).then((function(e){G.b.info("Bien ajouter")}))}})})}},{path:"/cars/edit/:id",name:"edit-cars",label:"Modifier v\xe9hicule",component:function(){var e=Object(p.g)().params,t=h().api,a=Object(n.useState)(!0),i=Object(o.a)(a,2),r=i[0],c=i[1],s=Object(n.useState)({}),u=Object(o.a)(s,2),d=u[0],b=u[1];return Object(n.useEffect)((function(){t.get("/cars/"+e.id).then((function(e){var t=e.data;b(t.data),c(!1)}))}),[]),Object(l.jsx)(V,{withBack:!0,loading:r,title:"Modifie v\xe9hicule : "+d.matricule,selectedSiderKey:"list-cars",children:Object(l.jsx)(J,{formItems:$,initialValues:d,onFinish:function(a){t.put("/cars/"+e.id,a).then((function(e){G.b.info("Bien modifier")}))}})})},hideInSide:!0}]},{name:"drivers",label:"Conducteurs",icon:Object(l.jsx)(g.a,{}),routes:[{path:"/drivers",name:"list-drivers",label:"List des conducteurs",component:function(){var e=h().api,t=Object(p.f)(),a=[{title:"Nom",dataIndex:"firstname",sorter:!0,hideInSearch:!0},{title:"Pr\xe9nom",dataIndex:"lastname",sorter:!0,hideInSearch:!0},{title:"TEL",dataIndex:"tel",sorter:!0,hideInSearch:!0,copyable:!0},{title:"Code paie",dataIndex:"code_paie",sorter:!0,hideInSearch:!0},{title:"Type",dataIndex:"type",sorter:!0,hideInSearch:!0},{title:"Date Cre\xe9ation",dataIndex:"created_at",valueType:"dateTime",hideInSearch:!0},{title:"option",valueType:"option",dataIndex:"id",render:function(a,n,i,r){return[Object(l.jsx)("a",{onClick:function(){e.delete("/drivers/"+n.id).then((function(){G.b.info("Bien Supprim\xe9"),r.reload()}))},children:"Supprimer"}),Object(l.jsx)("a",{onClick:function(){t.push("/drivers/edit/"+n.id)},children:"Modifier"})]}}],i=n.useRef();return window.actionRef=i,Object(l.jsx)(V,{title:"Utilisateurs",selectedSiderKey:"list-drivers",children:Object(l.jsx)(M.a,{actionRef:i,size:"small",search:!0,columns:a,ellipsis:!0,request:function(t,a,l){return console.log(a),a&&(t.sortBy=Object.keys(a)[0],t.sort=Object.values(a)[0]),e.get("/drivers",{params:t,sort:a}).then((function(e){return e.data}))},pagination:{defaultCurrent:1}})})},exact:!0},{path:"/drivers/add",name:"add-driver",label:"Nouveau conducteur",component:function(){var e=h().api;return Object(l.jsx)(V,{title:"Nauveau conducteur",selectedSiderKey:"add-drivers",children:Object(l.jsx)(J,{formItems:ee,onFinish:function(t){e.post("/drivers",t).then((function(e){G.b.info("Bien ajouter")}))}})})}},{path:"/drivers/edit/:id",name:"edit-driver",label:"Modifier conducteur",component:function(){var e=Object(p.g)().params,t=h().api,a=Object(n.useState)(!0),i=Object(o.a)(a,2),r=i[0],c=i[1],s=Object(n.useState)({}),u=Object(o.a)(s,2),d=u[0],b=u[1];return Object(n.useEffect)((function(){t.get("/drivers/"+e.id).then((function(e){var t=e.data;b(t.data),c(!1)}))}),[]),Object(l.jsx)(V,{withBack:!0,loading:r,title:"Modifie conducteur",selectedSiderKey:"list-drivers",children:Object(l.jsx)(J,{formItems:ee,initialValues:d,onFinish:function(a){t.put("/drivers"+e.id,a).then((function(e){G.b.info("Bien ajouter")}))}})})},hideInSide:!0}]},{name:"clients",label:"Clients",icon:Object(l.jsx)(g.a,{}),routes:[{path:"/clients",name:"list-clients",label:"List des clients",component:te,exact:!0},{path:"/clients/add",name:"add-client",label:"Nouveau client",component:function(){var e=h().api;return Object(l.jsx)(V,{title:"Nauveau client",selectedSiderKey:"add-clients",children:Object(l.jsx)(J,{formItems:ae,onFinish:function(t){e.post("/clients",t).then((function(e){G.b.info("Bien ajouter")}))}})})}},{path:"/clients/edit/:id",name:"edit-client",label:"Modifier client",component:function(){var e=Object(p.g)().params,t=h().api,a=Object(n.useState)(!0),i=Object(o.a)(a,2),r=i[0],c=i[1],s=Object(n.useState)({}),u=Object(o.a)(s,2),d=u[0],b=u[1];return Object(n.useEffect)((function(){t.get("/clients/"+e.id).then((function(e){var t=e.data;b(t.data),c(!1)}))}),[]),Object(l.jsx)(V,{withBack:!0,loading:r,title:"Modifie client",selectedSiderKey:"list-clients",children:Object(l.jsx)(J,{formItems:ae,initialValues:d,onFinish:function(a){t.put("/clients/"+e.id,a).then((function(e){G.b.info("Bien ajouter")}))}})})},hideInSide:!0}]},{name:"decharges",label:"Decharges",icon:Object(l.jsx)(g.a,{}),routes:[{path:"/decharges",name:"list-decharges",label:"List des decharges",component:te,exact:!0},{path:"/decharges/add",name:"add-decharge",label:"Nouveau decharge",component:function(){h().api;var e=Object(n.useState)({step:0}),t=Object(o.a)(e,2),a=t[0],i=t[1];window.allData=a;return Object(l.jsxs)(V,{title:"Nauveau decharge",children:[Object(l.jsxs)("div",{style:{display:"flex",flexDirection:"column"},children:[Object(l.jsxs)(le.a,{current:a.step,children:[Object(l.jsx)(re,{title:"Affectation client",description:""}),Object(l.jsx)(re,{title:"Checklist Conducteur",description:""}),Object(l.jsx)(re,{title:"Termin\xe9",description:""})]}),Object(l.jsx)("div",{children:Object(l.jsx)("hr",{})}),Object(l.jsxs)("div",{style:{flex:1},children:[0===a.step&&Object(l.jsx)(J,{formItems:ne,onFinish:function(e){i({data:Object(b.a)(Object(b.a)({},a),{},{data:e}),step:1})}}),1===a.step&&Object(l.jsx)(J,{formItems:ie,onFinish:function(e){i({data:Object(b.a)(Object(b.a)({},a),{},{data:e}),step:2})},initialValues:{cle_vehicule:1,starts:5}}),Object(l.jsx)("hr",{}),"checklist"]})]}),","]})}}]}]},be=E.a.Sider,Oe=I.a.SubMenu,je=function(){var e=Object(p.f)(),t=h().selectedSiderKey;return Object(l.jsx)(be,{style:{overflow:"auto",height:"calc(100vh - 48px)",marginTop:48,position:"fixed",left:0},children:Object(l.jsx)(I.a,{theme:"dark",mode:"inline",defaultSelectedKeys:t,children:de.routes.map((function(t,a){return t.routes?Object(l.jsx)(Oe,{icon:t.icon,title:t.label,children:t.routes.map((function(t,a){return t.hideInSide?void 0:Object(l.jsx)(I.a.Item,{onClick:function(){return e.push(t.path)},children:t.label},t.name)}))},t.name):Object(l.jsx)(I.a.Item,{onClick:function(){return e.push(t.path)},icon:t.icon,children:t.label},t.name)}))})})},he=E.a.Content,pe=function(){for(var e=[],t=0;t<100;t++)e.push(t);return Object(l.jsx)(m.a,{children:Object(l.jsxs)(E.a,{children:[Object(l.jsx)("div",{style:{position:"absolute",top:0,left:0,width:200,height:48,backgroundColor:"#001529",padding:5,textAlign:"center"},children:Object(l.jsx)(m.b,{to:"/",children:Object(l.jsx)("h1",{style:{color:"white"},children:"GestPark v1.0.0"})})}),Object(l.jsx)(je,{}),Object(l.jsxs)(E.a,{style:{marginLeft:200},children:[Object(l.jsx)(N,{}),Object(l.jsx)(he,{style:{paddingTop:48},children:de.routes.map((function(e,t){return Object(l.jsx)(p.c,{children:e.routes?(a=e.routes,Object(l.jsx)(p.c,{children:a.map((function(e,t){return Object(l.jsx)(p.a,{exact:e.exact,component:e.component,path:e.path},t)}))})):Object(l.jsx)(p.a,{exact:e.exact,component:e.component,path:e.path})});var a}))})]})]})})},me=a(252),Ee=a.n(me),fe=function(){var e=Object(n.useState)(localStorage.getItem("token")),t=Object(o.a)(e,2),a=t[0],l=t[1],i=function(e){localStorage.setItem("token",e),l(e)};return Object(n.useMemo)((function(){var e=Ee.a.create({baseURL:"/api",headers:{"x-access-token":a,Authorization:"Bearer ".concat(a),"Content-Type":"application/json",accept:"application/json"}});return e.interceptors.response.use((function(e){return e}),(function(e){return G.b.error(e.message),Promise.reject(e)})),{api:e,token:a,setToken:i}}),[a])};function Ae(){Object(p.f)();var e=fe(),t=e.api,a=e.setToken;return Object(l.jsx)("div",{className:"login",children:Object(l.jsxs)(K.a,{layout:"vertical",name:"basic",initialValues:{},onFinish:function(e){console.log("Success:",e),t.post("auth/login",e).then((function(e){var t=e.data;a(t.data.token),document.location.reload()})).catch((function(e){var t,a,l=(null===e||void 0===e||null===(t=e.response)||void 0===t||null===(a=t.data)||void 0===a?void 0:a.message)||""+e;G.b.error(l)}))},onFinishFailed:function(e){console.log("Failed:",e)},style:{border:"1px dashed black",padding:20},children:[Object(l.jsx)("h2",{children:"Authentification"}),Object(l.jsx)(K.a.Item,{label:"Username",name:"username",rules:[{required:!0,message:U}],children:Object(l.jsx)(B.a,{})}),Object(l.jsx)(K.a.Item,{label:"Password",name:"password",rules:[{required:!0,message:U}],children:Object(l.jsx)(B.a.Password,{})}),Object(l.jsx)(K.a.Item,{children:Object(l.jsx)(A.a,{type:"primary",htmlType:"submit",children:"Submit"})})]})})}function ve(){var e=Object(n.useState)(!0),t=Object(o.a)(e,2),a=t[0],i=t[1],r=Object(n.useState)(null),c=Object(o.a)(r,2),s=c[0],u=c[1],b=fe(),O=b.api,h=b.setToken,p=b.token,m=Object(n.useState)(""),E=Object(o.a)(m,2),f=E[0],A=E[1];return Object(n.useEffect)((function(){var e=!0;return i(!0),O.get("/auth/me").then((function(t){var a=t.data;e&&(u(a.data),i(!1))})).catch((function(){e&&(u(null),i(!1))})),function(){return e=!1}}),[p]),a?Object(l.jsx)("div",{style:{flex:1,display:"flex",justifyContent:"center",alignItems:"center"},children:Object(l.jsx)(d.a,{size:"large"})}):Object(l.jsx)(j,{value:{user:s,setUser:u,api:O,setToken:h,selectedSiderKey:f,setSelectedSiderKey:A},children:s?Object(l.jsx)(pe,{}):Object(l.jsx)(Ae,{})})}var Ie=function(e){e&&e instanceof Function&&a.e(3).then(a.bind(null,489)).then((function(t){var a=t.getCLS,l=t.getFID,n=t.getFCP,i=t.getLCP,r=t.getTTFB;a(e),l(e),n(e),i(e),r(e)}))};r.a.render(Object(l.jsx)(c.b,{locale:u.a,children:Object(l.jsx)(ve,{})}),document.getElementById("root")),Ie()}},[[470,1,2]]]);
//# sourceMappingURL=main.ae87648b.chunk.js.map