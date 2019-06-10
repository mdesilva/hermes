# Hermes: A dynamic secure form handler plugin

New install flow:

- Create forms table < create new table forms(id varchar(255) not null primary key, url varchar(255) not null);
- Create formSubmissions table < create new table formSubmissions(firstname varchar(255) null, lastname varchar(255) null, email varchar(255) null, errors text null, spam tinyint(1) not null,time date not null);
- Instantiate Admin panel
