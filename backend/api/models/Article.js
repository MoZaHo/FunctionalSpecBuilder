/**
 * Article.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

module.exports = {

  attributes: {
    chapter_id : {
      type : 'int',
      required : true,
      model : 'Chapter'
    },

    title : {
      type : 'string',
      required : true
    },

    order : {
      type : 'int'
    }

  }
};

