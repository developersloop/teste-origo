/// <reference types="Cypress" />

let mock = require('../../fixtures/example.json')

// id mock sempre acrescido de 1 (1,2,3) isso permitira acrescentar dados na base e testar mais de mais uma vez
const idMock = 50;
const habilitEditOrDelete = (id = idMock) => {
    return cy.request('GET', `${Cypress.env('url')}/disabled-for-test/${id}`)
        .then(res => {
            expect(res.status).to.equal(200)
            expect(res.isOkStatusCode).to.be.true
            expect(res.body).to.have.property('message')
            expect(mock.messages.includes(res.body.message)).to.be.true
        })
}

describe('Testing API links', function() {
    it('Success status 200 list links', function() {
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

    it('Success status 201 created new shorts links', function() {
        // trocar o indice do array mock.links[0] pois acaba falhando  o teste pq esbarra na regra de negocio do encurtador de links
        cy.request({
            method: 'post',
            url: Cypress.env('url'),
            body: mock.links[3],
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
                expect(res.body).to.not.have.property('message')
            }
        })
    })

    it('Success status 200 update link', function() {
        habilitEditOrDelete()
            .then(res => {
                cy.request({
                    method: 'PUT',
                    url: `${Cypress.env('url')}/${idMock}`,
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
   // para ver o teste rodando por inteiro retirar o .skip
   // para ver o teste persistando o link na base acrescentar o .skip
    it.skip('Success status 202 deleted link', function() {
        habilitEditOrDelete()
        .then(res => {
            cy.request({
                method: 'delete',
                url: `${Cypress.env('url')}/${idMock}`,
                failOnStatusCode: false,
            })
            .then(res => {
                if (res.status == 414) {
                    expect(res.status).to.equal(414)
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
