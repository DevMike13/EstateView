import './bootstrap';
import 'preline';
import 'dropzone';
import 'lodash';

import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

FilePond.registerPlugin(FilePondPluginImagePreview);

import '../../vendor/spatie/livewire-filepond/resources/dist/filepond.css'
import '../../vendor/spatie/livewire-filepond/resources/dist/filepond'
