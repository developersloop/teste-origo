/// <reference types="Cypress" />

let mock = require('../../fixtures/example.json')

const habilitEditOrDelete = (id = 36) => {
    return cy.request('GET', `${Cypress.env('url')}/disabled-for-test/${id}`)
        .then(res => {
            expect(res.status).to.equal(200)
            expect(res.isOkStatusCode).to.be.true
            expect(res.body).to.have.property('message')
            expect(mock.messages.includes(res.body.message)).to.be.true
        })
}

describe('Testing API links', function() {
    it.skip('Success status 200 list links', function() {
        cy.request({
            method: 'GET',
            url: Cypress.env('url'),
            failOnStatusCode: false,
          }).should(({ status, body}) => {
            expect(status).to.equal(200)
            expect(body).to.have.property('data')
            expect(body).to.have.property('message')

            if (body.data.length) expect(body.data.length > 0).to.equal(true)
            else expect(!body.data.length).to.be.true
        })
    })

    it.only('Success status 201 created new shorts links', function() {
        cy.request({
            method: 'post',
            url: Cypress.env('url'),
            body: { link: mock.links[0]},
            failOnStatusCode: false,
        })
        .then(res => {
            let containsStatus = [404,500].includes(res.status)
            if (containsStatus) {
                expect(containsStatus).to.be.true
                expect(res.isOkStatusCode).to.be.false
                expect(res.body).to.have.property('message')
                expect(!res.body.data.length).to.be.true
                expect(mock.messages.includes(res.body.message)).to.be.true

            }
            else  {
                expect(res.status).to.equal(200)
                expect(res.isOkStatusCode).to.be.true
                expect(res.body).to.have.property('message')
                expect(mock.messages.includes(res.body.message)).to.be.true
            }
        })
    })

    it.skip('Success status 200 update link', function() {
        habilitEditOrDelete()
            .then(res => {
                cy.request({
                    method: 'PUT',
                    url: `${Cypress.env('url')}/36`,
                    body: mock.links[1],
                    failOnStatusCode: false,
                })
                .then(res => {
                    let containsStatus = [404,500].includes(res.status)

                    if (containsStatus) {
                        expect(containsStatus).to.be.true
                        expect(res.isOkStatusCode).to.be.false
                        expect(res.body).to.have.property('message')
                        expect(!res.body.data.length).to.be.true
                        expect(mock.messages.includes(res.body.message)).to.be.true

                    }
                    else  {
                        expect(res.status).to.equal(200)
                        expect(res.isOkStatusCode).to.be.true
                        expect(res.body).to.have.property('message')
                        expect(mock.messages.includes(res.body.message)).to.be.true
                    }
                })
            })
    })
    it.skip('Success status 202 deleted link', function() {
        habilitEditOrDelete()
        .then(res => {
            cy.request({
                method: 'delete',
                url: `${Cypress.env('url')}/36`,
                failOnStatusCode: false,
            })
            .then(res => {
                console.log(res)
                if (res.status == 404) {
                    expect(res.status).to.equal(404)
                    expect(res.isOkStatusCode).to.be.false
                    expect(res.body).to.have.property('message')
                    expect(!res.body.data.length).to.be.true
                    expect(mock.messages.includes(res.body.message)).to.be.true

                }
                else  {
                    expect(res.status).to.equal(202)
                    expect(res.isOkStatusCode).to.be.true
                    expect(res.body).to.have.property('message')
                    expect(mock.messages.includes(res.body.message)).to.be.true
                }
            })
        })
    })
})
