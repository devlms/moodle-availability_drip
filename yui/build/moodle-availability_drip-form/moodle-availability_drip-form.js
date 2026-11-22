YUI.add('moodle-availability_drip-form', function (Y, NAME) {

/**
 * Form for the days since enrolment condition.
 *
 * @module moodle-availability_drip-form
 */
M.availability_drip = M.availability_drip || {};

/**
 * @class M.availability_drip.form
 * @extends M.core_availability.plugin
 */
M.availability_drip.form = Y.Object(M.core_availability.plugin);

M.availability_drip.form.getNode = function(json) {
    var id = 'availability_drip_' + M.availability_drip.form.counter++;
    var html = '<label for="' + id + '" class="me-2">' + M.util.get_string('label_days', 'availability_drip') + '</label>' +
        '<input type="number" min="0" max="3650" step="1" class="form-control d-inline-block w-auto" name="d" id="' + id + '"> ' +
        '<span>' + M.util.get_string('unit_days', 'availability_drip') + '</span>';
    var node = Y.Node.create('<span class="availability-group">' + html + '</span>');
    if (json.d !== undefined) {
        node.one('input[name=d]').set('value', json.d);
    }
    if (!M.availability_drip.form.addedEvents) {
        M.availability_drip.form.addedEvents = true;
        var root = Y.one('.availability-field');
        root.delegate('valuechange', function() {
            M.core_availability.form.update();
        }, '.availability_drip input[name=d]');
    }
    return node;
};

M.availability_drip.form.counter = 0;

M.availability_drip.form.fillValue = function(value, node) {
    var raw = node.one('input[name=d]').get('value');
    value.d = /^\d+$/.test(raw) ? parseInt(raw, 10) : raw;
};

M.availability_drip.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);
    if (typeof value.d !== 'number' || value.d < 0 || value.d > 3650) {
        errors.push('availability_drip:error_days');
    }
};

}, '@VERSION@', {"requires": ["base", "node", "event", "event-valuechange", "moodle-core_availability-form"]});
